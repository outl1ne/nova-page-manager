<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Builder;

class Page extends TemplateModel
{
    public static $type = 'page';

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->type = 'page';
        });

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'page');
        });
    }

    public function parent()
    {
        return $this->belongsTo(Page::class);
    }
}
