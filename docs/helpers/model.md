# Model Helper

This helper will provide information about specific model, which can be loaded from full classname `ModelHelper::fromClass()` or from an model's file `ModelHelper::fromFile()`.

::: tip
Model Helper is "macroable" and "collectable", which allows you to run batch methods and collect output with `ModelHelper::fromClass('App\User')->collect('name', 'namespace')`
:::

## Construct from File
<helper-method method="fromFile">
    /**
     * Load a model from file
     * @param string $path
     * @return Model
     */
</helper-method>

## Construct from Class
<helper-method method="fromClass">
    /**
     * Load a model from class
     * @param string $class
     * @return Model
     */
</helper-method>

## Reflection
<helper-method method="reflection">
    /**
     * Get class reflection
     * @return ReflectionClass
     * @throws \ReflectionException
     */
</helper-method>

## Parent
<helper-method method="parent">
    /**
     * Get immediate parent of a model class
     * Note that this might not be useful as user can extend from custom 'model' class not Laravel one.
     * @return string
     */
</helper-method>

## Class
<helper-method method="class">
    /**
     * Get model's class (namespace + name)
     * @return string
     */
</helper-method>

## Name
<helper-method method="name">
    /**
     * Get model's name without namespace
     * @return string
     */
</helper-method>

## Namespace
<helper-method method="namespace">
    /**
     * Get models's namespace
     * @return string
     */
</helper-method>

## Instance
<helper-method method="instance">
    /**
     * Initiate a new instance of a model
     * @return Eloquent
     */
</helper-method>

## Primary
<helper-method method="primary">
    /**
     * Get primary key for a model
     * @return string
     */
</helper-method>

## Table
<helper-method method="table">
    /**
     * Get model's table
     * @return string
     */
</helper-method>

## Hidden
<helper-method method="hidden">
    /**
     * Get hidden attributes
     * @return Collection
     */
</helper-method>

## Fillable
<helper-method method="fillable">
    /**
     * Get fillable attributes
     * @return Collection
     */
</helper-method>

## Request
<helper-method method="fillable">
    /**
     * Get FormRequest for this model if one exists
     * @return null|FormRequest
     */
</helper-method>

## Attributes
<helper-method method="attributes">
    /**
     * Get model's attributes
     * @return Collection
     */
</helper-method>

## Rules
<helper-method method="rules">
    /**
     * Build validation rules
     * @param bool $fillableOnly: true
     * @param bool $deepAnalysis: false
     * @return Collection
     */
</helper-method>

## Relationships
<helper-method method="relationships">
    /**
     * Get model's relationships
     * @return Collection
     */
</helper-method>

## Fields
<helper-method method="fields">
    /**
     * Get attribute and relationship fields (either native HTML or Vue extended)
     * This method basically maps attribute type to corresponding field/input type.
     * @param bool $fillable: default false
     * @param bool $extended: default false
     * @return Collection
     */
</helper-method>
