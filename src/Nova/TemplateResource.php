<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Resource;
use OptimistDigital\NovaPageManager\NovaPageManager;
use Illuminate\Http\Request;
use OptimistDigital\NovaPageManager\Nova\Filters\TemplateLocaleFilter;
use OptimistDigital\NovaPageManager\Nova\Filters\TemplateChildrenFilter;

abstract class TemplateResource extends Resource
{
    protected $templateClass;

    protected function getTemplateClass()
    {
        if (isset($this->templateClass)) return $this->templateClass;

        $templates = $this->type === 'page'
            ? NovaPageManager::getPageTemplates()
            : NovaPageManager::getRegionTemplates();

        if (isset($this->template)) {
            foreach ($templates as $template) {
                if ($template::$name == $this->template) $this->templateClass = new $template;
            }
        }

        return $this->templateClass;
    }

    protected function getTemplateFields(): array
    {
        $templateClass = $this->getTemplateClass();
        if (isset($templateClass)) return $templateClass->_getTemplateFields(request());
        return [];
    }

    public function filters(Request $request)
    {
        return [
            new TemplateLocaleFilter,
            new TemplateChildrenFilter,
        ];
    }
}
