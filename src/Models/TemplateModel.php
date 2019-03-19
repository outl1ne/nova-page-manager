<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    protected $table = 'nova_page_manager';

    protected $casts = [
        'data' => 'object'
    ];
}
