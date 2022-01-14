<?php

namespace OptimistDigital\NovaPageManager;

use Illuminate\Http\Request;
use OptimistDigital\NovaPageManager\Core\TemplateTypes;

class Template
{
    public static $type = TemplateTypes::PAGE;
    public static $slug = '';

    protected $resource = null;

    public function __construct($resource = null)
    {
        $this->resource = $resource;
    }

    public function fields(Request $request)
    {
        return [];
    }

    public function seoFields(Request $request)
    {
        return true;
    }
}
