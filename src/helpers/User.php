<?php

namespace Fibers\Helper\Helpers;

use Fibers\Helper\Facades\ModelsHelper;
use Fibers\Helper\Traits\Collect;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;

class User
{
    use Collect, Macroable;

    private $classname;
    private $reflection;
    private $model;
    private $filepath;
    private $implements;

    public function __construct()
    {
        $this->classname = config("auth.providers.users.model");
    }

    /**
     * Get user's model class
     * @return string
     */
    public function class (): string
    {
        return $this->classname;
    }

    /**
     * Get user's model name
     * @return string
     */
    public function name (): string
    {
        return class_basename($this->classname);
    }

    /**
     * Get user's model reflection class
     * @return ReflectionClass
     * @throws \Exception
     */
    public function reflection (): ReflectionClass
    {
        if ($this->reflection) return $this->reflection;
        try {
            return $this->reflection = new ReflectionClass($this->classname);
        } catch (\ReflectionException $e) {
            throw new \Exception("User model was not found. Are you sure that namespace is set correctly?");
        }
    }

    /**
     * Get user's model ModelHelper
     * @return Model
     */
    public function model (): Model
    {
        if ($this->model) return $this->model;

        return $this->model = ModelsHelper::get($this->classname);
    }

    /**
     * Check if user's model exists
     * @return bool
     */
    public function exists (): bool
    {
        try {
            return !blank($this->filepath());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get filepath of user's model
     * @return string
     * @throws \Exception
     */
    public function filepath (): string
    {
        if ($this->filepath) return $this->filepath;

        return $this->filepath = $this->reflection()->getFileName();
    }

    /**
     * Get full content of user's model file
     * @return string
     * @throws \Exception
     */
    public function content (): string
    {
        return file_get_contents($this->filepath());
    }

    /**
     * Get collection of 'implements' for user's model
     * @return Collection
     */
    public function implements (): Collection
    {
        if ($this->implements) return $this->implements;

        return $this->implements = collect(class_implements($this->classname))->keys();
    }

}
