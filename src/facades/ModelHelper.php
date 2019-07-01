<?php

namespace Fibers\Helper\Facades;

use Fibers\Helper\Helpers\Model;
use Illuminate\Support\Facades\Facade;

class ModelHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Model::class;
    }
}
