<?php

namespace Outl1ne\PageManager\Facades;

use \Illuminate\Support\Facades\Facade;

class NPMCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Outl1ne\PageManager\NPMCache::class;
    }
}
