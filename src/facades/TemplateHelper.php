<?php

namespace Fibers\Helper\Facades;

use Fibers\Helper\Helpers\Template;
use Illuminate\Support\Facades\Facade;

class TemplateHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Template::class;
    }
}
