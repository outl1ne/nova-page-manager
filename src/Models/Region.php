<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'nova-regions';

    protected $casts = [
        'data' => 'array'
    ];
}
