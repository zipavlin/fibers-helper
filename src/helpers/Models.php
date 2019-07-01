<?php
/*
|--------------------------------------------------------------------------
| Fibers models helpers
|--------------------------------------------------------------------------
|
| Assorted models helpers to get additional information about models
| in general (not a specific one), like folder name, folder path, list of
| model files and list of model classes.
|
*/

namespace Fibers\Helper\Helpers;

use Fibers\Helper\Facades\ModelHelper;
use Fibers\Helper\Traits\Collect;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Models
{
    use Collect, Macroable;

    private $path;
    private $dir;
    private $list;
    private $files;

    public function info()
    {
        return (Object) [
            "dir" => $this->dir(),
            "path" => $this->path(),
            "files" => $this->files(),
            "list" => $this->list(),
        ];
    }

    /**
     * Get models' directory name
     * @return string
     */
    public function dir(): string
    {
        if ($this->dir) return $this->dir;
        return $this->dir = ltrim(str_replace(app_path(), "", $this->path()), DIRECTORY_SEPARATOR);
    }

    /**
     * Get models' directory path
     * Path is set optimistically - meaning: it does not check if any files exist inside or if those files are models.
     * @return string
     */
    public function path(): string
    {
        if ($this->path) return $this->path;

        // set path using standard laravel conventions
        if (file_exists(config_path('admin.php') and $dir = config('admin.models_dir') and isset($dir) and !empty($dir))) {
            $path = app_path(config('admin.models_dir'));
        }
        elseif (file_exists(app_path("Models"))) {
            $path = app_path("Models");
        }
        elseif (file_exists(app_path("models"))) {
            $path = app_path("models");
        }
        else {
            $path = app_path();
        }

        // return path if we are optimistic about users using conventions
        return $this->path = $path;
    }

    /**
     * Get collection of models
     * @param string|null $path
     * @return Collection
     */
    public function list(string $path = null): Collection
    {
        if ($this->list) return $this->list;

        return $this->files($path)->map(function ($filepath) {
            return (new Model())->fromFile($filepath);
        });
    }

    /**
     * Get list of models' classes
     * @param string|null $path
     * @return Collection
     */
    public function classes(string $path = null): Collection
    {
        return $this->list($path)->map(function ($model) {
            return $model->class();
        });
    }

    /**
     * Get list of models' names
     * @param string|null $path
     * @return Collection
     */
    public function names(string $path = null): Collection
    {
        return $this->list($path)->map(function ($model) {
            return $model->name();
        });
    }

    /**
     * Get most commonly used namespace
     * @return string
     */
    public function namespace(): string
    {
        return rtrim($this->classes()->countBy(function ($class) {
            return Str::before($class, class_basename($class));
        })->sort()->keys()->last(), "\\");
    }

    /**
     * Get list of models' files
     * @param string|null $path
     * @return Collection
     */
    public function files(string $path = null): Collection
    {
        if ($this->files) return $this->files;

        // find all files in models dir
        return $this->files = collect(glob(($path ?? $this->path()) . "/*.{php,PHP}", GLOB_BRACE))->map(function ($filepath) {
            return str_replace("\\", "/", realpath($filepath));
        });
    }

    /**
     * Get User model as defined in auth provider
     * @return string
     */
    public function user(): string
    {
        return config("auth.providers.users.model");
    }

    /**
     * Search for model by an approximate name string
     * @param string $name
     * @return Model
     */
    public function search(string $name): Model
    {
        $model = null;

        // load a list of models
        $models = $this->list()->mapWithKeys(function ($model) {
            return [strtolower($model->name()) => $model];
        });

        // normalize name
        $name = strtolower(Str::singular($name));

        // try exact match
        if ($models->has($name)) $model = $name;
        // return best match
        else {
            $shortest = -1;
            foreach ($models->keys() as $key) {
                $lev = levenshtein($name, $key);
                if ($lev == 0) { $model = $key; break; }
                if ($lev <= $shortest || $shortest < 0) {
                    $model  = $key;
                    $shortest = $lev;
                }
            }
        }

        // return model's class
        return $models->get($model);
    }

    /**
     * Get full class from name
     * @param string $name
     * @return string
     */
    public function class(string $name): string
    {
        if (Str::studly(Str::singular($name)) === Str::studly(Str::singular(class_basename($name)))) {
            return $this->namespace().'\\'.ltrim(class_basename(Str::studly(Str::singular($name))), '\\');
        } else {
            return $name;
        }
    }

    /**
     * Get table from name
     * @param string $name
     * @return string
     */
    public function table (string $name): string
    {
        return Str::snake(class_basename($name));
    }

    /**
     * Get class
     * @param string $name
     * @return Model|null
     */
    public function get(string $name)
    {
        $name = $this->class($name);
        return $this->exists($name) ? ModelHelper::fromClass($name) : null;
    }

    /**
     * Check if model exists
     * @param string $name
     * @return bool
     */
    public function exists (string $name): bool
    {
        // check if name is class or not
        if (Str::ucfirst(Str::camel(Str::singular($name))) === class_basename($name)) $name = $this->class($name);
        return $this->classes()->contains($name);
    }
}
