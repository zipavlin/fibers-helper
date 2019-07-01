<?php

namespace Fibers\Helper\Helpers;

use Fibers\Helper\Traits\Collect;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class View
{
    use Collect, Macroable;

    private $layoutsDir;
    private $layoutsName;
    private $layouts;
    private $componentsDir;
    private $componentsName;
    private $components;
    private $paths;
    private $folders;

    /**
     * Get layouts dir
     * @return string|null
     */
    public function layoutsPath ()
    {
        if ($this->layoutsDir) return $this->layoutsDir;
        return $this->layoutsDir = $this->folders()->first(function ($item) {
            return in_array(basename($item), ['layouts', 'layout']);
        });
    }

    /**
     * Get layouts dir name
     * @return string|null
     */
    public function layoutsName()
    {
        if ($this->layoutsName) return $this->layoutsName;
        return $this->layoutsName = $this->layoutsPath() ? basename($this->layoutsPath()) : null;
    }

    /**
     * Get layout components
     * @return Collection
     */
    public function layouts (): Collection
    {
        if ($this->layouts) return $this->layouts;
        return $this->layouts = collect(glob($this->layoutsPath()."/*.blade.php"))->map(function ($path) {
            return Str::before(basename($path), ".blade.php");
        });
    }

    /**
     * Get components path
     * @return string|null
     */
    public function componentsPath ()
    {
        if ($this->componentsDir) return $this->componentsDir;
        return $this->componentsDir = $this->folders()->first(function ($item) {
            return in_array(basename($item), ['components', 'component', 'partials', 'partial', 'includes', 'include']);
        });
    }

    /**
     * Get components dir name
     * @return string|null
     */
    public function componentsName()
    {
        if ($this->componentsName) return $this->componentsName;
        return $this->componentsName = $this->componentsPath() ? basename($this->componentsPath()) : null;
    }

    /**
     * Get components
     * @return Collection
     */
    public function components (): Collection
    {
        if ($this->components) return $this->components;
        return $this->components = collect(glob($this->componentsPath()."/*.blade.php"))->map(function ($path) {
            return Str::before(basename($path), ".blade.php");
        });
    }

    /**
     * Get main template that others extend
     * @return string|null
     */
    public function main ()
    {
        return $this->layouts()->first(function ($item) {
            return in_array(Str::before(basename($item), ".blade"), ['app', 'main']);
        });
    }

    /**
     * Check if header component is defined and return it
     * @return string|null
     */
    public function header()
    {
        return $this->components()->first(function ($item) {
            return in_array(basename($item), ['header', '_header']);
        });
    }

    /**
     * Check if footer component is defined and return it
     * @return string|null
     */
    public function footer()
    {
        return $this->components()->first(function ($item) {
            return in_array(basename($item), ['footer', '_footer']);
        });
    }

    /**
     * Get first valid path or view
     * @return string|null
     */
    public function path ()
    {
        return $this->paths()->first();
    }

    /**
     * Get all paths
     * @return Collection
     */
    private function paths (): Collection
    {
        if ($this->paths) return $this->paths;
        return $this->paths = collect(config('view.paths'))->filter(function ($path) {
            return file_exists($path);
        });
    }

    /**
     * Get all view folders
     * @return Collection
     */
    private function folders (): Collection
    {
        if ($this->folders) return $this->folders;
        // get all folders in views dir
        return $this->folders = $this->paths()->map(function ($path) {
            // get all folders in this folder
            return glob("$path/*", GLOB_ONLYDIR);
        })->flatten()->unique()->map(function ($path) {
            return realpath($path);
        });
    }
}
