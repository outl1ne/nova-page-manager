<?php

namespace OptimistDigital\NovaPageManager\Nova\Flexible;

use Whitecube\NovaFlexibleContent\Value\Resolver;

class TemplateResolver extends Resolver
{
    protected function extractValueFromResource($resource, $attribute)
    {
        $value = data_get($resource, str_replace('->', '.', $attribute)) ?? [];
        if (is_string($value)) $value = json_decode($value) ?? [];

        return collect($value)->map(function ($item) {
            if (is_array($item)) return (object)$item;
            return $item;
        })->toArray();
    }
}
