<?php

namespace Fibers\Helper\Facades;

use Fibers\Helper\Helpers\Memory;
use Illuminate\Support\Facades\Facade;

class MemoryHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Memory::class;
    }
}
