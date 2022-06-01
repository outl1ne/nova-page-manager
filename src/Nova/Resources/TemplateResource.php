<?php

namespace Outl1ne\PageManager\Nova\Resources;

use Illuminate\Support\Arr;
use Laravel\Nova\Resource;
use Outl1ne\PageManager\NPM;
use Outl1ne\PageManager\Template;

abstract class TemplateResource extends Resource
{
    protected $template;
    protected $templateConfig;

    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->templateConfig = $this->getTemplateConfig();

        $className = $this->templateConfig['class'] ?? null;
        $this->template = $className ? new $className : null;
    }

    private function getTemplateConfig()
    {
        $templates = $this->type === Template::TYPE_PAGE
            ? NPM::getPageTemplates()
            : NPM::getRegionTemplates();

        return Arr::first($templates, fn ($t) => $t['slug'] === $this->resource->template);
    }
}
