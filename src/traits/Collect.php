<?php


namespace Fibers\Helper\Traits;


use Illuminate\Support\Str;

trait Collect
{
    /**
     * Collect batch data
     * @param string ...$info
     * @return object
     */
    public function collect (string ...$info)
    {
        $export = [];
        foreach ($info as $method) {
            $method = Str::camel($method);
            if (!method_exists($this, $method)) continue;
            $export[$method] = $this->{$method}();
        }
        return (Object) $export;
    }
}
