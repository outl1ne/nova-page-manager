<?php

namespace Outl1ne\PageManager\Nova\Resources;

use Laravel\Nova\Resource;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
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

    public function authorizedToReplicate(Request $request)
    {
        return !($this->templateConfig['unique'] ?? false);
    }

    public function getTemplateOptions($type)
    {
        $isPageType = $type === Template::TYPE_PAGE;

        $model = $isPageType ? NPM::getPageModel() : NPM::getRegionModel();
        $templates = $isPageType ? NPM::getPageTemplates() : NPM::getRegionTemplates();

        $existingTemplates = $model::select('template')->groupBy('template')->get()->pluck('template')->toArray();
        $templates = Arr::where($templates, function ($template) use ($existingTemplates) {
            // Is not unique
            if (!($template['unique'] ?? false)) return true;

            $isSameTemplate = $template['slug'] === ($this->templateConfig['slug'] ?? null);
            $templateAlreadyExists = in_array($template['slug'], $existingTemplates);

            return $templateAlreadyExists ? $isSameTemplate : true;
        });

        $options = [];
        foreach ($templates as $slug => $template) {
            $options[$slug] = (new $template['class'])->name(request());
        }

        return $options;
    }
}
