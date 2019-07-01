<?php

namespace Fibers\Helper\Facades;

use Fibers\Helper\Helpers\User;
use Illuminate\Support\Facades\Facade;

class UserHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return User::class;
    }
}
