<?php

namespace Fibers\Helper\Facades;

use Illuminate\Support\Facades\Facade;

class Fibers extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "Fibers";
    }
}
