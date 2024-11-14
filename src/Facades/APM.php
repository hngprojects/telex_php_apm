<?php

namespace TelexAPM\Facades;

use Illuminate\Support\Facades\Facade;

class APM extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'apm';
    }
}
