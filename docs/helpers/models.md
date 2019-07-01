# Models Helper

This helper will provide information about general settings used in models, like namespace, directory, etc.

::: tip
Models Helper is "macroable" and "collectable", which allows you to run batch methods and collect output with `ModelsHelper::collect('dir', 'path')`
:::

## Dir
<helper-method method="dir">
    /**
     * Get models' directory name
     * @return string
     */
</helper-method>

## Path
<helper-method method="path">
    /**
     * Get models' directory path
     * Path is set optimistically - meaning: it does not check if any files exist inside or if those files are models.
     * @return string
     */
</helper-method>

## List
<helper-method method="list">
    /**
     * Get collection of models
     * @param string|null $path
     * @return Collection
     */
</helper-method>

## Classes
<helper-method method="classes">
    /**
     * Get list of models' classes
     * @param string|null $path
     * @return Collection
     */
</helper-method>

## Names
<helper-method method="names">
    /**
     * Get list of models' names
     * @param string|null $path
     * @return Collection
     */
</helper-method>

## Namespace
<helper-method method="namespace">
    /**
     * Get most commonly used namespace
     * @return string
     */
</helper-method>

## Files
<helper-method method="files">
    /**
     * Get list of models' files
     * @param string|null $path
     * @return Collection
     */
</helper-method>

## User
<helper-method method="user">
    /**
     * Get User model as defined in auth provider
     * @return string
     */
</helper-method>

## Search
<helper-method method="search">
    /**
     * Search for model by an approximate name string. It will always return a closest model.
     * @param string $name
     * @return Model
     */
</helper-method>

## Class
<helper-method method="class">
    /**
     * Build full class from name
     * @param string $name
     * @return string
     */
</helper-method>

## Table
<helper-method method="table">
    /**
     * Get table from name
     * @param string $name
     * @return string
     */
</helper-method>

## Get
<helper-method method="get">
    /**
     * Get class if one exists
     * @param string $name
     * @return Model|null
     */
</helper-method>

## Exists
<helper-method method="exists">
    /**
     * Check if model exists
     * @param string $name
     * @return bool
     */
</helper-method>
