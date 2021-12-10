<?php

namespace OptimistDigital\NovaPageManager;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return NovaPageManagerCache::class;
    }
}
