<?php
/*
|--------------------------------------------------------------------------
| Fibers memory helpers
|--------------------------------------------------------------------------
|
| Assorted global memory helpers
|
*/

namespace Fibers\Helper\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Traits\Macroable;

class Memory
{
    use Macroable;

    protected $storage;

    public function __construct()
    {
        // create a new global variable if one doesnt exist yet
        global $fibers;
        if (!isset($fibers) or blank($fibers)) $fibers = (Object) [
            'messages' => [],
            'commands' => [],
            'chain' => []
        ];
        $this->storage = $fibers;
    }

    /**
     * Collect global storage array by key
     * @param string $key
     * @return Collection
     */
    public function get(string $key): Collection
    {
        return collect($this->storage->{strtolower($key)});
    }

    /**
     * Update in-memory array
     * @return Memory
     */
    public function update(): Memory
    {
        // set new global
        global $fibers;
        $fibers = $this->storage;

        // chain
        return $this;
    }

    /**
     * Replace all data in keyed array
     * @param string $key
     * @param string|array $data
     * @return Memory
     */
    public function set(string $key, $data): Memory
    {
        // set new data
        $this->storage->{strtolower($key)} = $data;

        // update global store
        $this->update();

        // chain
        return $this;
    }

    /**
     * Push data to keyed array
     * @param string $key
     * @param string|array $data
     * @return Memory
     */
    public function push(string $key, $data): Memory
    {
        // set new data
        array_push($this->storage->{strtolower($key)}, $data);

        // update global store
        $this->update();

        // chain
        return $this;
    }

    /**
     * Put data in keyed array using name as a second-level key
     * @param string $key
     * @param string $name
     * @param string|array $data
     * @return Memory
     */
    public function put(string $key, string $name, $data): Memory
    {
        // set new data
        $this->storage->{strtolower($key)}[$name] = $data;

        // update global store
        $this->update();

        // chain
        return $this;
    }

    /**
     * Add message to in-memory storage of messages
     * @param string $message
     * @param string $type
     * @return Memory
     */
    public function addMessage(string $message, string $type = "success"): Memory
    {
        $this->push('messages', [
            "type" => $type,
            "content" => $message
        ]);
        return $this;
    }

    /**
     * Add command to in-memory queue of commands
     * @param string $command
     * @param array $arguments
     * @return Memory
     */
    public function addCommand(string $command, array $arguments): Memory
    {
        $command = strtolower($command);
        $this->put('commands', $command, $arguments);
        return $this;
    }

    /**
     * Run all commands that are in in-memory queue
     */
    public function runCommands(): void
    {
        $this->get('commands')->each(function ($item, $key) {
            Artisan::call($key, $item);
        });
    }

    /**
     * Remove keyed array from in-memory storage
     * @param string $key
     * @return Memory
     */
    public function delete(string $key): Memory
    {
        // set empty storage
        $this->storage->{$key} = [];

        // update global
        $this->update();

        // chain
        return $this;
    }

    /**
     * Delete all data from in-memory storage, emptying all storage
     * @return Memory
     */
    public function deleteAll(): Memory
    {
        // set empty storage
        $this->storage = (Object) [
            'messages' => [],
            'commands' => [],
            'chain' => []
        ];

        // update global
        $this->update();

        // chain
        return $this;
    }

    /**
     * Start an in-memory chain or add item to it
     * @param string $uuid
     */
    public function startChain(string $uuid): void
    {
        $this->push('chain', $uuid);
    }

    /**
     * End in-memory chain of commands, if uuid is the same that started a chain.
     * Run all commands from queue and return collection of messages.
     * @param string $uuid
     * @return bool|Collection
     */
    public function endChain(string $uuid)
    {
        $output = false;

        if ($this->get('chain')->get(0) === $uuid) {
            // run all commands
            $this->runCommands();

            // returns messages and errors
            $output = $this->get('messages');

            // clear cache
            $this->deleteAll();
        }

        return $output;
    }
}
