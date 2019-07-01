# View Helper

This helper will provide information about app views, layout folder, layout components, folder of partials, etc.

::: tip
View Helper is "macroable" and "collectable", which allows you to run batch methods and collect output with `ViewHelper::collect('layoutsPath', 'componentsPath')`
:::

## Layouts Path
<helper-method method="layoutsPath">
    /**
     * Get layouts dir
     * @return string|null
     */
</helper-method>

## Layouts Name
<helper-method method="layoutsName">
    /**
     * Get layouts dir name
     * @return string|null
     */
</helper-method>

## Layouts
<helper-method method="layouts">
    /**
     * Get layout components
     * @return Collection
     */
</helper-method>

## Components Path
<helper-method method="componentsPath">
    /**
     * Get components path
     * @return string|null
     */
</helper-method>

## Components Name
<helper-method method="componentsName">
    /**
     * Get components dir name
     * @return string|null
     */
</helper-method>

## Components
<helper-method method="components">
    /**
     * Get components
     * @return Collection
     */
</helper-method>

## Main Layout
<helper-method method="main">
    /**
     * Get main template that others extend
     * @return string|null
     */
</helper-method>

## Header Partial
<helper-method method="header">
    /**
     * Check if header component is defined and return it
     * @return string|null
     */
</helper-method>

## Footer Partial
<helper-method method="footer">
    /**
     * Check if footer component is defined and return it
     * @return string|null
     */
</helper-method>

## Path
<helper-method method="path">
    /**
     * Get first valid path or view
     * @return string|null
     */
</helper-method>

## Paths
<helper-method method="paths">
    /**
     * Get all paths
     * @return Collection
     */
</helper-method>

## Folders
<helper-method method="folders">
    /**
     * Get all view folders
     * @return Collection
     */
</helper-method>
