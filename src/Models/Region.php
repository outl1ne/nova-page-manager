<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Builder;

class Region extends TemplateModel
{
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->type = 'region';
        });

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'region');
        });
    }
}
