<?php

use OptimistDigital\NovaPageManager\Models\Page;
use OptimistDigital\NovaPageManager\Models\Region;
use Illuminate\Support\Collection;
use OptimistDigital\NovaPageManager\Models\TemplateModel;
use OptimistDigital\NovaPageManager\NovaPageManager;

// ------------------------------
// nova_get_pages_structure
// ------------------------------

if (!function_exists('nova_get_pages_structure')) {
    function nova_get_pages_structure()
    {
        $formatPages = function (Collection $pages) use (&$formatPages) {
            $data = [];
            $pages->each(function ($page) use (&$data, &$formatPages) {
                $localeChildren = Page::where('locale_parent_id', $page->id)->get();
                $_pages = collect([$page, $localeChildren])->flatten();
                $_data = [
                    'locales' => $_pages->pluck('locale'),
                    'id' => $_pages->pluck('id', 'locale'),
                    'name' => $_pages->pluck('name', 'locale'),
                    'slug' => $_pages->pluck('slug', 'locale'),
                    'template' => $page->template,
                ];

                $children = Page::where('parent_id', $page->id)->get();
                if ($children->count() > 0) {
                    $_data['children'] = $formatPages($children);
                }

                $data[] = $_data;
            });
            return $data;
        };

        $parentPages = Page::whereNull('parent_id')->whereNull('locale_parent_id')->get();
        return $formatPages($parentPages);
    }
}


// ------------------------------
// nova_get_regions
// ------------------------------

if (!function_exists('nova_get_regions')) {
    function nova_get_regions()
    {
        $formatRegions = function (Collection $regions) {
            $data = [];
            $regions->each(function ($region) use (&$data) {
                $localeChildren = Region::where('locale_parent_id', $region->id)->get();
                $_regions = collect([$region, $localeChildren])->flatten();
                $data[] = [
                    'locales' => $_regions->pluck('locale'),
                    'id' => $_regions->pluck('id', 'locale'),
                    'name' => $_regions->pluck('name', 'locale'),
                    'template' => $region->template,
                    'data' => $_regions->pluck('data', 'locale'),
                ];
            });
            return $data;
        };

        $parentRegions = Region::whereNull('locale_parent_id')->get();
        return $formatRegions($parentRegions);
    }
}


// ------------------------------
// nova_get_page
// ------------------------------

if (!function_exists('nova_get_page')) {

    function nova_get_page($pageId)
    {
        if (empty($pageId)) return null;
        $page = Page::find($pageId);
        if (empty($page)) return null;

        return [
            'locale' => $page->locale,
            'id' => $page->id,
            'name' => $page->name,
            'slug' => $page->slug,
            'data' => nova_resolve_template_model_data($page),
            'template' => $page->template,
        ];
    }
}


// ------------------------------
// nova_resolve_template_field_value
// ------------------------------

if (!function_exists('nova_resolve_template_field_value')) {
    function nova_resolve_template_field_value($field, $fieldValue)
    {
        return method_exists($field, 'resolveResponseValue')
            ? $field->resolveResponseValue($fieldValue)
            : $fieldValue;
    }
}


// ------------------------------
// nova_resolve_template_model_data
// ------------------------------

if (!function_exists('nova_resolve_template_model_data')) {
    function nova_resolve_template_model_data(TemplateModel $templateModel)
    {
        // Find the Template class for the model
        foreach (NovaPageManager::getTemplates() as $tmpl) {
            if ($tmpl::$name === $templateModel->template) $templateClass = $tmpl;
        }

        // Fail silently is template is no longer registered
        if (!isset($templateClass)) return null;

        // Get the template's fields
        $fields = collect((new $templateClass)->fields(request()));

        $resolvedData = [];
        foreach (((array)$templateModel->data) as $fieldName => $fieldValue) {
            $field = $fields->where('attribute', $fieldName)->first();
            if (!isset($field)) continue;

            if ($field->component === 'nova-flexible-content') {
                $resolvedData[$fieldName] = nova_resolve_flexible_fields_data($field, $fieldValue);
                continue;
            }

            $resolvedData[$fieldName] = nova_resolve_template_field_value($field, $fieldValue);
        }
        return $resolvedData;
    }
}


// ------------------------------
// nova_resolve_flexible_fields_data
// ------------------------------

if (!function_exists('nova_resolve_flexible_fields_data')) {
    function nova_resolve_flexible_fields_data($field, $flexibleFieldValue)
    {
        // Accessing protected property helper
        $accessProtectedProperty = function ($object, $property) {
            $reflection = new ReflectionClass($object);
            $_property = $reflection->getProperty($property);
            $_property->setAccessible(true);
            return $_property->getValue($object);
        };

        $flexibleLayouts = $accessProtectedProperty($field, 'layouts');

        $resolvedData = [];
        foreach ($flexibleFieldValue as $layoutValue) {
            foreach ($flexibleLayouts as $layout) {
                $layoutName = $accessProtectedProperty($layout, 'name');
                if ($layoutName !== $layoutValue->layout) continue;

                $layoutFields = $accessProtectedProperty($layout, 'fields');

                $row = [];

                foreach ($layoutValue->attributes as $fieldName => $fieldValue) {
                    $subField = $layoutFields->where('attribute', $fieldName)->first();
                    if (!isset($subField)) continue;
                    $row[$fieldName] = nova_resolve_template_field_value($subField, $fieldValue);
                }

                $resolvedData[] = $row;
            }
        }
        return $resolvedData;
    }
}
