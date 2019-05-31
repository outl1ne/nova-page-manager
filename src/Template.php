<?php

namespace OptimistDigital\NovaPageManager;

use Illuminate\Http\Request;

abstract class Template
{
    public static $type = 'page';
    public static $name = '';
    public static $seo = false;

    abstract function fields(Request $request, $locale): array;
}
