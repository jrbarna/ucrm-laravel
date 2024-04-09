<?php

namespace jrbarna\UcrmLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class UcrmLaravel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ucrm-laravel';
    }
}
