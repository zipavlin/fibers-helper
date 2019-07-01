<?php

namespace Fibers\Helper\Facades;

use Fibers\Helper\Helpers\Models;
use Illuminate\Support\Facades\Facade;

class ModelsHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Models::class;
    }
}
