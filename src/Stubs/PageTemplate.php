<?php

namespace App\Nova\Templates;

use Illuminate\Http\Request;
use OptimistDigital\NovaPageManager\Template;

class :className extends Template
{
    public static $type = ':type';
    public static $name = ':name';
    public static $seo = false;
    public static $view = null;

    public function fields(Request $request): array
    {
        return [];
    }
}
