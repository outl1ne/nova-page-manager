<?php

namespace OptimistDigital\NovaPageManager\Models;

use NovaPageManagerCache;
use OptimistDigital\NovaPageManager\NovaPageManager;

class Region extends TemplateModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NovaPageManager::getRegionsTableName());
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            NovaPageManagerCache::clear();
        });
    }
}
