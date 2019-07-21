<?php
/*
|--------------------------------------------------------------------------
| Fibers template helpers
|--------------------------------------------------------------------------
|
| Assorted template helpers to help with template creation. Mostly used
| internally in commands.
|
*/

namespace Fibers\Helper\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Template
{
    use Macroable;

    protected $file;
    protected $template;
    protected $output;

    /**
     * Load template from file
     * @param string $filename
     * @return Template
     * @throws \Exception
     */
    public function fromFile(string $filename): Template
    {
        // check if filename is a path or really just name and set path to fibers root if true
        if ($filename === basename($filename)) $filename = FIBERS_ROOT . '/templates/' . $filename;

        // check if file exists and load its content
        if (file_exists($filename)) {
            $this->template = file_get_contents($filename);
            return $this;
        }
        else throw new \Exception("File $filename does not exist");
    }

    /**
     * Load template from string
     * @param string $string
     * @return Template
     */
    public function fromString(string $string): Template
    {
        $this->template = $string;
        return $this;
    }

    /**
     * Does the actual string replacing
     * Expected input it keyed array of changes ("name:modifier" => content).
     * It supports some content modifiers like:
     * list, array and string.
     *
     * @param array $changes
     * @return Template
     * @throws \Exception
     */
    public function replace (array $changes = null): Template
    {
        // do initial check
        if (!$this->template) throw new \Exception("Cannot replace template parameters, because template was not loaded. Did you call Template->load() method?");

        // prepare changes collection
        // key:modifier|conditional [array, list, string]
        $changes = collect($changes)->mapWithKeys(function ($item, $key) {
            $name = Str::before($key, ":");
            $options = collect(explode(",", Str::after($key, ":")));
            $modifier = $options->intersect(["array", "list", "string"])->first();
            switch ($modifier) {
                case "array": $value = $this->array($item); break;
                case "list": $value = $this->list($item); break;
                case "string": $value = $this->string($item); break;
                default: $value = (is_array($item) or is_object($item)) ? collect($item)->join("\n\n") : $item;
            }
            if ($options->contains("conditional")) {
                $value = !blank($item) ? "protected \$$name = $value;" : "// protected \$$name = [];";
            }
            return ['{{'.$name.'}}' => $value];
        });

        // replace
        if ($changes) {
            $this->output = str_replace($changes->keys()->toArray(), $changes->values()->toArray(), $this->template);
        } else {
            $this->output = $this->template;
        }

        // chain
        return $this;
    }

    /**
     * Write template output to file
     * @param string $filepath
     * @return string
     * @throws \Exception
     */
    public function toFile (string $filepath): string
    {
        // do initial check
        if (!$this->output) throw new \Exception("Cannot write file, because output was not set. Did you call Template->replace() method?");

        // check if folder exists
        if (!file_exists($dir = dirname($filepath))) {
            mkdir($dir, 0755, true);
        }

        // write file
        file_put_contents($filepath, $this->output);

        // output
        return $this->output;
    }

    /**
     * Output template
     * @return string
     * @throws \Exception
     */
    public function toString (): string
    {
        // do initial check
        if (!$this->output) throw new \Exception("Cannot output string, because output was not set. Did you call Template->replace() method?");

        // return output
        return $this->output;
    }

    // helpers

    /**
     * Build an list string from an array or Collection
     * @param array|Collection $value
     * @return string
     */
    public function list ($value): string
    {
        $isAssociative = Arr::isAssoc(collect($value)->toArray());
        return collect($value)
            ->when($isAssociative, function ($collection) {
                return $collection->map(function ($item, $key) {
                    return $this->string($key) . " => " . $this->string($item);
                });
            })
            ->unless($isAssociative, function ($collection) {
                return $collection->map(function ($item) {
                    return $this->string($item);
                });
            })
            ->values()
            ->join(", ");
    }

    /**
     * Build an array string from an array or Collection
     * @param array|Collection $value
     * @return string
     */
    public function array ($value): string
    {
        return "[" . $this->list($value) . "]";
    }

    /**
     * Build an escaped string
     * @param string $value
     * @return string
     */
    public function string (string $value): string
    {
        return "'$value'";
    }

    /**
     * Build an integer from value
     * @param int|string $value
     * @return int
     */
    public function integer ($value): int
    {
        return intval($value);
    }
}
