<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    protected $fillable = ['parent_id'];

    protected $casts = [
        'data' => 'object'
    ];
}
