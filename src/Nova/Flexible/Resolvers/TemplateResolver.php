<?php

namespace OptimistDigital\NovaPageManager\Nova\Flexible\Resolvers;

use Whitecube\NovaFlexibleContent\Value\Resolver;

class TemplateResolver extends Resolver
{
    protected function extractValueFromResource($resource, $attribute)
    {
        return data_get($resource, str_replace('->', '.', $attribute));
    }
}
