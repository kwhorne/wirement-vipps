<?php

namespace Wirement\Vipps\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wirement\Vipps\Vipps
 */
class Vipps extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vipps';
    }
}
