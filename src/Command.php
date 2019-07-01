<?php
/*
|--------------------------------------------------------------------------
| Fibers Command class
|--------------------------------------------------------------------------
|
| This abstract class is used internally by fibers commands. It sets automatic
| writing to command chain, additional input methods, additional output methods
| and some helper methods used by other methods.
|
| TODO: [uuid, softdelete, image, images, files, media] attribute should set model appropriately
| Example:
id
title -> string (255)
published -> boolean, hidden
published_at -> date, format:d-m-Y
user -> relationship (belongs-to-many), eager
place -> relationship (ho)
timestamps
softdelete
*/

namespace Fibers\Helper;

use Fibers\Helper\Facades\MemoryHelper;
use Fibers\Helper\Facades\TemplateHelper;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Console\Parser;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    protected $events;
    protected $stream;
    protected $uuid;
    protected $files;
    protected $composer;
    protected $signature;
    protected $success = false;
    protected $silent = false;
    protected $force = false;
    protected $incognito = false;

    /**
     * Command constructor.
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        // set files and composer helpers
        $this->files = $files;
        $this->composer = $composer;

        // add additional options
        [$name, $arguments, $options] = Parser::parse(
    "{--S|silent : skip unnecessary checks and prompts }
               {--F|force : force file rewrites }
               {--ignore=* : commands to ignore when continuing this command }");
        $this->getDefinition()->addOptions($options);

        // spin events
        if (!$this->incognito) $this->events = App::make(Dispatcher::class);
    }

    /**
     * Runs the command.
     * @param  InputInterface  $input  Input Interface.
     * @param  OutputInterface $output Output Interface.
     * @return integer
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->stream = $input->getStream() ?: STDIN;

        if (!$this->incognito) {
            // register self in chain
            $this->uuid = (String)Str::uuid();
            MemoryHelper::startChain($this->uuid);

            // fire starting event
            $this->events->dispatch(
                new Events\CommandStart($this, $input)
            );
        }

        // run code and get exit code
        $exitCode = parent::run($input, $output);

        if (!$this->incognito) {
            // fire ending event
            $this->events->dispatch(
                new Events\CommandEnd($this, $input, $exitCode)
            );

            // unregister self from chain
            if ($output = MemoryHelper::endChain($this->uuid)) {
                $this->summary($output);
            }
        }

        // end
        return $exitCode;
    }

    /**
     * Register silent option
     */
    public function handle()
    {
        $this->silent = $this->option('silent');
        $this->force = $this->option('force');
    }

    /**
     * @param string $name
     * @param \Closure|array $callable
     * @param bool $prompt
     */
    protected function continue (string $name, $callable, $prompt = null)
    {
        $prompt = $prompt ?? "Would you like to create $name as well?";
        if ($this->success and !in_array($name, $this->option('ignore'))) {
            if (is_callable($callable) and ($this->option($name) or (!$this->silent and !$this->option("silent")))) {
                $callable();
            } elseif (is_array($callable) and (($this->option('silent') and $this->option($name)) or $this->confirm($prompt))) {
                $this->call("fibers:make:$name", array_merge([
                    "--force" => $this->force,
                    "--silent" => $this->silent
                ], $callable));
            }
        }
    }

    /**
     * Start a new multiline console input
     * @param string $message
     * @param array $comments
     * @return Collection
     */
    protected function multiline($message = "Please enter your input", $comments = []): Collection
    {
        // prepare comments
        $comments = collect($comments)->merge(['!q|:exit=exit'])->map(function ($item) {
            return "<comment>$item</>";
        })->join(", ");

        // write intro
        $this->output->writeln(
            " <info>$message</info> [$comments]\n"
        );

        // collect input
        $input = collect();
        while (!feof($this->stream)) {
            $row = trim(fgets($this->stream));
            if ($row === ":exit" or $row === "!q" or $row === "!Q" or empty($row)) {
                break;
            } else {
                $input->push($row);
            }
        }
        return $input;
    }

    /**
     * Open nano editor or fallback to multiline input
     * @return Collection
     */
    protected function editor (): Collection
    {
        $nano = exec("nano --version");
        // it doesn't work on windows so we will load simple multiline
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' OR blank($nano)) {
            return $this->multiline();
        }
        else {
            system("nano fibers-editor-input > `tty`");
            // check if file exists
            if (file_exists(base_path('fibers-editor-input'))) {
                // get content
                $content = file_get_contents(base_path('fibers-editor-input'));
                // remove file
                unlink(base_path('fibers-editor-input'));
                // return collections
                return collect(explode("\n", $content))->filter(function ($item) {
                    return !blank(trim($item));
                });
            } else {
                return collect();
            }
        }
    }

    /**
     * Select with (optionally) multiple answers
     * @param $message
     * @param $choices
     * @param null $default
     * @param bool $multiple
     * @return string
     */
    protected function pick ($message, $choices, $default = null, $multiple = false)
    {
        if ($this->extendedTerminal()) {
            return $this->select(" $message", $choices, $multiple);
        } else {
            $default = !blank($default) ? (is_array($default) ? ($multiple ? implode(",", $default) : $default[0]) : $default) : null;
            return $this->choice("$message [<comment>comma separated values</comment>]", $choices, $default, 1, $multiple);
        }
    }

    /**
     * Output card styled info
     * @param $title
     * @param $content
     * @param $footer
     */
    protected function card($title, $content, $footer)
    {
        // constants
        $padding = 4; // width padding is 4 characters (| <content> |)

        // get terminal length
        $terminalLength = exec('tput cols') - $padding;

        // collect content
        $content = collect($content);
        $footer = collect($footer);

        // get length of content
        $contentLength = collect([$content->concat($footer)->reduce(function ($carry, $item) {
            $item = is_array($item) ? $item['content'] : $item;
            if (($len = strlen($item)) > $carry) $carry = $len;
            return $carry;
        }, 0), 60])->max();

        // get max length
        $maxLength = ($contentLength < $terminalLength) ? $contentLength : $terminalLength;

        // prepare constants
        $divider = "+-".str_repeat("-", $maxLength)."-+";
        $empty = "| " . str_repeat(" ", $maxLength) . " |";
        $title = "| ".(str_repeat(" ", floor(($maxLength - strlen($title)) / 2))).$title.(str_repeat(" ", ceil(($maxLength - strlen($title)) / 2)))." |";

        // prepare content
        $content = $content->map(function ($item) use ($maxLength) {
            $item = is_array($item) ? $item["content"] : $item;
            // color code files
            return collect(explode("\n", wordwrap($item, $maxLength, "\n")))->map(function ($row) use ($maxLength) {
                // if row is as long as content
                if (strlen($row) === $maxLength) {
                    return "| $row |";
                } else {
                    return "| $row" . str_repeat(" ", $maxLength - strlen($row)) . " |";
                }
            })->join("\n");
        })->join("\n");
        $footer = $footer->map(function ($item, $index) use ($maxLength) {
            $prefix = is_array($item) ? ($item['type'] === 'error' ? '×' : '»') : '-';
            $item = is_array($item) ? $item['content'] : $item;
            return collect(explode("\n", wordwrap($item, $maxLength - 2, "\n")))->map(function ($row, $rowIndex) use ($maxLength, $index, $prefix) {
                $row = (strlen($row) === $maxLength - 2) ? $row : ($row . str_repeat(" ", $maxLength - strlen($row) - 2));
                $row = ($rowIndex === 0 ? $prefix : " ") . " " . $row;
                return "| $row |";
            })->join("\n");
        })->join("\n");

        $this->line($divider);
        $this->line($title);
        $this->line($divider);
        $this->line($empty);
        $this->line($content);
        $this->line($empty);
        if (!blank($footer)) {
            $this->line($divider);
            $this->line($footer);
        }
        $this->line($divider);
    }

    /**
     * Helper function for outputting command chain results
     * @param $content
     */
    protected function summary ($content): void
    {
        $content = collect($content)->groupBy("type");
        $this->card("FIBERS JOB FINISHED", $content->get('success'), collect()->concat($content->get('error') ?? [])->concat($content->get('info') ?? []));
    }

    /**
     * Helper method for template building
     * @param string $template
     * @param array $changes
     * @return string
     */
    protected function replace(string $template, array $changes): string
    {
        return TemplateHelper::fromString($template)->replace($changes)->toString();
    }

    /**
     * Call command when chain finishes
     * @param $command
     * @param array $arguments
     */
    protected function callDelayed($command, $arguments = []): void
    {
        MemoryHelper::addCommand($command, $arguments);
    }

    /**
     * Add message that will be shown at the end
     * @param $message
     * @param string $type
     */
    protected function infoDelayed($message, $type = "success")
    {
        MemoryHelper::addMessage($message, $type);
    }

    /**
     * Can we use advance terminal options
     */
    protected function extendedTerminal()
    {
        $sttyMode = shell_exec('stty -g');
        shell_exec('stty -icanon -echo');
        $sttyMode_alt = shell_exec('stty -g');

        shell_exec(sprintf('stty %s', $sttyMode));
        return $sttyMode !== $sttyMode_alt;
    }
}
