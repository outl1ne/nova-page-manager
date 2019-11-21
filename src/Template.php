<?php

namespace OptimistDigital\NovaPageManager;

use Illuminate\Http\Request;

abstract class Template
{
    public static $type = 'page';
    public static $name = '';
    public static $seo = false;
    public static $view = null;

    protected $resource = null;

    public function __construct($resource = null)
    {
        $this->resource = $resource;
    }

    abstract function fields(Request $request): array;
}
