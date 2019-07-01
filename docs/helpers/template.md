# Template Helper

This helper is used to build file templates from string `TemplateHelper::fromString()` or from a stub file `TemplateHelper::fromFile()`.

::: tip
Template Helper is "macroable", which allows you to add additional methods to the TemplateHelper class at run time.
:::

## Construct from File
<helper-method method="fromFile">
    /**
     * Load template from file
     * @param string $filename
     * @return Template
     * @throws \Exception
     */
</helper-method>

## Construct from String
<helper-method method="fromString">
    /**
     * Load template from string
     * @param string $string
     * @return Template
     */
</helper-method>

## Replace
<helper-method method="replace">
    /**
     * Does the actual string replacing
     * Expected input it keyed array of changes ("name:modifier" => content).
     * It supports some content modifiers like: list, array and string.
     * @param array $changes: keyed array, which supports modifiers
     * @return Template
     * @throws \Exception
     */
</helper-method>

## Build to File
<helper-method method="toFile">
    /**
     * Write template output to file
     * @param string $filepath
     * @return string
     * @throws \Exception
     */
</helper-method>

## Build to String
<helper-method method="toString">
    /**
     * Output template
     * @return string
     * @throws \Exception
     */
</helper-method>

## List
<helper-method method="list">
    /**
     * Build an list string from an array or Collection
     * @param array|Collection $value
     * @return string
     */
</helper-method>

## Array
<helper-method method="array">
    /**
     * Build an array string from an array or Collection
     * @param array|Collection $value
     * @return string
     */
</helper-method>

## String
<helper-method method="string">
    /**
     * Build an escaped string
     * @param string $value
     * @return string
     */
</helper-method>

## Integer
<helper-method method="integer">
    /**
     * Build an integer from value
     * @param int|string $value
     * @return int
     */
</helper-method>
