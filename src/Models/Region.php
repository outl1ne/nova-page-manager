<?php

namespace Outl1ne\PageManager\Models;

use NPMCache;
use Outl1ne\PageManager\NPM;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
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
