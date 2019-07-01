<?php

namespace Fibers\Helper\Facades;

use Fibers\Helper\Helpers\View;
use Illuminate\Support\Facades\Facade;

class ViewHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return View::class;
    }
}
