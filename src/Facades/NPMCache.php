<?php

namespace OptimistDigital\NovaPageManager\Facades;

use \Illuminate\Support\Facades\Facade;

class NPMCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OptimistDigital\NovaPageManager\NPMCache::class;
    }
}
