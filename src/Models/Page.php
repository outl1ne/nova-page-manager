<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'nova-pages';

    protected $casts = [
        'data' => 'array'
    ];
}
