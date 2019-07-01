# User Helper

This helper load a user's model defined in `config/auth.providers.users.model` and return detailed info about it.

::: tip
User Helper is "macroable" and "collectable", which allows you to run batch methods and collect output with `UserHelper::collect('class', 'namespace')`
:::

## Class
<helper-method method="class">
    /**
     * Get user's model class
     * @return string
     */
</helper-method>

## Name
<helper-method method="name">
    /**
     * Get user's model name
     * @return string
     */
</helper-method>

## Reflection
<helper-method method="reflection">
    /**
     * Get user's model reflection class
     * @return ReflectionClass
     * @throws \Exception
     */
</helper-method>

## Model
<helper-method method="model">
    /**
     * Get user's model ModelHelper
     * @return Model
     */
</helper-method>

## Exists
<helper-method method="exists">
    /**
     * Check if user's model exists
     * @return bool
     */
</helper-method>

## Filepath
<helper-method method="filepath">
    /**
     * Get filepath of user's model
     * @return string
     * @throws \Exception
     */
</helper-method>

## Content
<helper-method method="content">
    /**
     * Get full content of user's model file
     * @return string
     * @throws \Exception
     */
</helper-method>

## Implements
<helper-method method="implements">
    /**
     * Get collection of 'implements' for user's model
     * @return Collection
     */
</helper-method>
