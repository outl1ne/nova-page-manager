<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Resource;
use OptimistDigital\NovaLocaleField\Filters\LocaleFilter;
use OptimistDigital\NovaPageManager\NovaPageManager;

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
                if ($template::$name == $this->template) $this->templateClass = new $template($this->resource);
            }
        }

        return $this->templateClass;
    }

    /**
     * Gets the template fields and separates them into an
     * array of two keys: 'fields' and 'panels'.
     *
     * @return array
     **/
    protected function getTemplateFieldsAndPanels(): array
    {
        $templateClass = $this->getTemplateClass();
        $templateFields = [];
        $templatePanels = [];

        $handleField = function (&$field) {
            if (!empty($field->attribute) && ($field->attribute !== 'ComputedField')) {
                if (empty($field->panel)) {
                    $field->attribute = 'data->' . $field->attribute;
                } else {
                    $sanitizedPanel = nova_page_manager_sanitize_panel_name($field->panel);
                    $field->attribute = 'data->' . $sanitizedPanel . '->' . $field->attribute;
                }
            } else {
                if ($field instanceof \Laravel\Nova\Fields\Heading) {
                    return $field->hideFromDetail();
                }
            }

            if (method_exists($field, 'hideFromIndex')) {
                return $field->hideFromIndex();
            }

            return $field;
        };

        if (isset($templateClass)) {
            $rawFields = $templateClass->fields(request());

            foreach ($rawFields as $field) {
                // Handle Panel
                if ($field instanceof \Laravel\Nova\Panel) {
                    $field->data = array_map(function ($_field) use (&$handleField) {
                        return $handleField($_field);
                    }, $field->data);

                    $templatePanels[] = $field;
                    continue;
                }

                // Handle Field
                $templateFields[] = $handleField($field);
            }
        }

        return [
            'fields' => $templateFields,
            'panels' => $templatePanels,
        ];
    }

    public function filters(Request $request)
    {
        if (NovaPageManager::hasNovaLang()) return [];

        return [
            (new LocaleFilter($this->resource->getTable() . '.locale'))->locales(NovaPageManager::getLocales()),
        ];
    }
}
