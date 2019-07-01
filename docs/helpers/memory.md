# Memory Helper

This helper will streamline memory storage using key-value multidimensional array. It is used primarily to build, store and run an in-memory queue of commands and its output messages.

::: tip
Model Helper is "macroable", which allows you to add additional methods to the MemoryHelper class at run time.
:::

## Get
<helper-method method="get">
    /**
     * Collect global storage array by key
     * @param string $key
     * @return Collection
     */
</helper-method>

## Update
<helper-method method="update">
    /**
     * Update in-memory array
     * @return Memory
     */
</helper-method>

## Set
<helper-method method="set">
    /**
     * Replace all data in keyed array
     * @param string $key
     * @param string|array $data
     * @return Memory
     */
</helper-method>

## Push
<helper-method method="push">
    /**
     * Push data to keyed array
     * @param string $key
     * @param string|array $data
     * @return Memory
     */
</helper-method>

## Put
<helper-method method="put">
    /**
     * Put data in keyed array using name as a second-level key
     * @param string $key
     * @param string $name
     * @param string|array $data
     * @return Memory
     */
</helper-method>

## Add Message
<helper-method method="addMessage">
    /**
     * Add message to in-memory storage of messages
     * @param string $message
     * @param string $type
     * @return Memory
     */
</helper-method>

## Add Command
<helper-method method="addCommand">
    /**
     * Add command to in-memory queue of commands
     * @param string $command
     * @param array $arguments
     * @return Memory
     */
</helper-method>

## Run Commands
<helper-method method="runCommands">
    /**
     * Run all commands that are in in-memory queue
     */
</helper-method>

## Delete
<helper-method method="delete">
    /**
     * Remove keyed array from in-memory storage
     * @param string $key
     * @return Memory
     */
</helper-method>

## Delete All
<helper-method method="deleteAll">
    /**
     * Delete all data from in-memory storage, emptying all storage
     * @return Memory
     */
</helper-method>

## Start a Chain
<helper-method method="startChain">
    /**
     * Start an in-memory chain or add item to it
     * @param string $uuid
     */
</helper-method>

## End a Chain
<helper-method method="endChain">
    /**
     * End in-memory chain of commands, if uuid is the same that started a chain.
     * Run all commands from queue and return collection of messages.
     * @param string $uuid
     * @return bool|Collection
     */
</helper-method>
