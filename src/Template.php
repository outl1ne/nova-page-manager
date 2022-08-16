<?php

namespace Outl1ne\PageManager;

use Illuminate\Http\Request;
use Outl1ne\PageManager\Traits\DataReplaceHelpers;

class Template
{
    use DataReplaceHelpers;

    const TYPE_PAGE = 'page';
    const TYPE_REGION = 'region';

    protected $type = null; // 'page' or 'region'
    protected $resource = null;

    public function __construct($resource = null)
    {
        $this->resource = $resource;
        $this->type = NPM::getTemplateClassType(static::class);
    }

    public function name(Request $request): string
    {
        return class_basename(static::class);
    }

    public function fields(Request $request): array
    {
        return [];
    }

    public function resolve($pageOrRegion, $params): array
    {
        return [];
    }

    public function pathSuffix(): string|null
    {
        return null;
    }
}
