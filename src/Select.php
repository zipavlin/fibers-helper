<?php
namespace Fibers\Helper;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Select
{
    private $input;
    private $output;
    private $stream;
    private $stty;
    private $xLimit = 0;
    private $yLimit = 0;
    private $x = 0;
    private $y = 0;
    private $c = []; // content array
    private $choices;

    public function __construct(InputInterface $input, OutputInterface $output, $stream, $choices)
    {
        $this->input = $input;
        $this->output = $output;
        $this->stream = $stream;
        $this->choices = $choices;

        try {
            $this->prepareTerminal();
            return $this;
        } catch (\Exception $exception) {
            $this->restoreTerminal();
            throw $exception;
        }
    }

    private function inputLoop ()
    {
        while (!feof($this->stream)) {
            $char = fread($this->stream, 1);
            // Backspace Character
            if ("\177" === $char) {
               $this->backspace();
            }
            // arrow keys navigation
            elseif ("\033" === $char) {
                $char .= fread($this->stream, 2);
                if (in_array($char[2], ['A', 'B', 'C', 'D'])) {
                    switch ($char[2]) {
                        case 'A': $this->up(); break;
                        case 'B': $this->down(); break;
                        case 'C': $this->right(); break;
                        case 'D': $this->left(); break;
                    }
                }
            }
            // ctrl+X
            elseif (24 === ord($char)) {
                $this->end();
                break;
            }
            // return key
            elseif (10 === ord($char)) {
                $this->return();
            }
            // delete key
            elseif (126 === ord($char)) {
                $this->delete();
            }
            else {
                $this->char($char);
            }
        }
    }

    // handle 'UP' key
    private function up($simple = false)
    {
        if ($this->y > 0) {
            $this->y--;
            $this->control("1A");
            // reposition cursor to end of row if this is this position doesn't exist
            if (!$simple && $this->length() < $this->x) {
                $this->x = $this->length();
                $this->moveCursor();
            }
        }
    }

    // handle 'DOWN' key
    private function down($simple = false)
    {
        if ($this->y < count($this->c) - 1) {
            $this->y++;
            $this->control("1B");
            // reposition cursor to end of row if this is this position doesn't exist
            if (!$simple && $this->length() < $this->x) {
                $this->x = $this->length();
                $this->moveCursor();
            }
        }
    }

    // handle 'RIGHT' key
    private function right($simple = false): void
    {
        if ($this->x < $this->length()) {
            $this->x++;
            $this->control("1C");
        }
        // check if we can move to beginning of next line
        /* this is too bugy to release
        elseif (!$simple && $this->y < count($this->c) - 1) {
            $this->y++;
            $this->x = 0;
            $this->moveCursor($this->x, $this->y);
        }
        */
    }

    // handle 'LEFT' key
    private function left($simple = false): void
    {
        if ($this->x > 0) {
            $this->x--;
            $this->control("1D");
        }
        // check if we can move to end of prev line
        /* this is too bugy to release
        elseif (!$simple && $this->y > 0) {
            $this->y--;
            $this->x = $this->length();
            $this->moveCursor($this->x, $this->y);
        }
        */
    }

    // handle 'RETURN' key
    private function return()
    {
        $this->y++;
        $this->x = 0;
        // if this is new row, just prepare it
        if (!isset($this->c[$this->y])) {
            array_push($this->c, "");
        }
        // if this row exists -> push new row in this index
        else {
            $this->c = array_splice($this->c, $this->y, 0, "");
        }
        $this->output->write("\n\x0D");
    }

    // handle 'SPACE' key
    private function space()
    {

    }

    // write row
    private function writeRow($row = null, $keepCursor = false)
    {
        $this->clearRow($row);
        // write new line
        $this->output->write($this->c[$this->y]);
        // keep cursor in the same position
        if ($keepCursor) {
            $this->x++;
            $this->moveCursor();
        }
        // move cursor at the end of this line
        else {
            $this->endOfRow();
        }
    }

    // clear row
    private function clearRow($row = null)
    {
        // move cursor to this row
        $this->moveCursor(0, $this->y);
        // clear line
        $this->control("2K");
    }

    private function writeDisplay()
    {
        
    }

    // clear whole display
    private function clearDisplay()
    {
        if ($this->options->fullscreen) {
            $this->control("2J");
        } else {
            $this->start();
            $this->control("0J");
        }
    }

    // move cursor to the end
    private function end()
    {
        $this->moveCursor($this->length(count($this->c) - 1), count($this->c) - 1);
    }

    // move cursor to end of row
    private function endOfRow()
    {
        $this->moveCursor($this->length() - 1, $this->y);
    }

    // move cursor to position
    private function moveCursor($x = null, $y = null)
    {
        $y = ($y ?? $this->y) + $this->yLimit;
        $x = ($x ?? $this->x) + $this->xLimit;
        $this->control("{$y};{$x}H");
    }

    // move cursor to start
    private function start()
    {
        $this->control("{$this->yLimit};{$this->xLimit}H");
    }

    // length of row
    private function length($row = null): int
    {
        return !isset($this->c[$row ?? $this->y]) ? 0 : strlen($this->c[$row ?? $this->y]);
    }

    // write control character
    private function control($char)
    {
        $this->output->write("\x1B[$char");
    }

    // prepare terminal
    private function prepareTerminal()
    {
        // save stty mode
        $this->stty = shell_exec('stty -g');

        // set stty (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        // get current cursor position
        $this->control("6n");
        $buf = fread($this->stream, 16);
        preg_match("/(?:.*?)(\d+);(\d+)/", $buf, $matches);
        if ($matches) {
            if (isset($matches[1])) $this->yLimit = intval($matches[1]) - 1;
            if (isset($matches[2])) $this->xLimit = intval($matches[2]) - 1;
        }
        if ($this->yLimit < 1) $this->yLimit = 1;
        if ($this->xLimit < 1) $this->xLimit = 1;

        // clear the rest of terminal
        $this->clearDisplay();

        // prepare header if full screen
        if ($this->options->fullscreen) {
            if ($this->options->header) {
                $rows = explode("\n", $this->options->header);
                // add header count to yLimit
                $this->yLimit = count($rows) + 1;
                // write header
                foreach ($rows as $row) {
                    $this->output->writeLn($row);
                }
                $this->output->write("\n");
            } elseif ($this->options->title) {
                // add header count to yLimit
                $this->yLimit = 4 + ($this->options->comment ? 2 : 0);
                // get width of terminal
                $width = intval(exec('tput cols'));
                $this->output->writeln("+" . str_repeat("-", $width - 2) . "+");
                $this->output->writeln("|".(str_repeat(" ", floor(($width - 2 - strlen($this->options->title)) / 2))).$this->options->title.(str_repeat(" ", ceil(($width - 2 - strlen($this->options->title)) / 2)))."|");
                $this->output->writeln("+" . str_repeat("-", $width - 2) . "+");
                if ($this->options->comment) {
                    $this->output->writeln("| > " . $this->options->comment . str_repeat(" ", $width - strlen($this->options->comment) - 5) . "|");
                    $this->output->writeln("+" . str_repeat("-", $width - 2) . "+");
                }
                $this->output->write("\n");
            }
        }
    }

    // restore terminal
    private function restoreTerminal()
    {
        if ($this->options->fullscreen) {
            $this->control("2J");
        }
        shell_exec(sprintf('stty %s', $this->stty));
        $this->output->write("\n");
    }

    // export as string
    public function asString (): string
    {
        return $this->asCollection()->join("\n");
    }

    // export as array
    public function asArray(): array
    {
        return $this->asCollection()->all();
    }

    // export as collection
    public function asCollection(): Collection
    {
        // wait for input
        $this->inputLoop();

        // restore terminal
        $this->restoreTerminal();

        return collect($this->c)->filter(function ($item) {
            return !blank($item);
        });
    }

    // load string
    // Note: this is quite buggy
    public function loadString(string $string)
    {
        return $this->loadArray(explode("\n", $string));
    }

    // load array
    // Note: this is quite buggy
    public function loadArray($array)
    {
        try {
            // populate content array
            $this->c = collect($array)->toArray();
            // write lines
            for ($i = 0; $i < count($this->c); $i++) {
                // move cursor to this line
                $this->moveCursor(0, $this->yLimit + $i);
                // write line
                $this->output->writeLn($this->c[$i]);
            }
            // move to start
            $this->moveCursor(0, -(count($this->c) - 1));
            $this->x = 0;
            $this->y = 0;
            // chain
            return $this;
        } catch (\Exception $exception) {
            $this->restoreTerminal();
            throw $exception;
        }
    }

    // load file
    // this is too buggy to release
    /*
    public function loadFile(string $filepath)
    {
        $string = file_get_contents($filepath);
        return $this->loadString($string);
    }
    */
}
