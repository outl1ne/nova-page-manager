<?php

namespace OptimistDigital\NovaPageManager;

use Illuminate\Http\Request;

abstract class Template
{
    public static $type = 'page';
    public static $name = '';
    public static $seo = false;

    abstract function fields(Request $request): array;

    public function _getTemplateFields(Request $request)
    {
        return array_map(function ($field) {
            if (!empty($field->attribute)) {
                $field->attribute = 'data->' . $field->attribute;
            }

            return $field;
        }, $this->fields($request));
    }
}
