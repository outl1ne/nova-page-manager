<?php

namespace Outl1ne\NovaPageManager\Facades;

use \Illuminate\Support\Facades\Facade;

class NPMCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Outl1ne\NovaPageManager\NPMCache::class;
    }
}
