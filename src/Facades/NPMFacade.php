<?php

namespace OptimistDigital\NovaPageManager;

use \Illuminate\Support\Facades\Facade;

class NPMFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return NPMCache::class;
    }
}
