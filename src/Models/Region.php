<?php

namespace Outl1ne\NovaPageManager\Models;

use NPMCache;
use Outl1ne\NovaPageManager\NPM;

class Region extends TemplateModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NPM::getRegionsTableName());
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            NPMCache::clear();
        });
    }
}
