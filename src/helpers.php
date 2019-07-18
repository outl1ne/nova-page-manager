<?php

use OptimistDigital\NovaPageManager\Models\Page;
use OptimistDigital\NovaPageManager\Models\Region;
use Illuminate\Support\Collection;
use OptimistDigital\NovaPageManager\Models\TemplateModel;
use OptimistDigital\NovaPageManager\NovaPageManager;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Heading;

// ------------------------------
// nova_get_pages_structure
// ------------------------------

if (!function_exists('nova_get_pages_structure')) {
    function nova_get_pages_structure($previewToken = null)
    {
        $formatPages = function (Collection $pages) use (&$formatPages, $previewToken) {
            $data = [];
            $pages->each(function ($page) use (&$data, &$formatPages, $previewToken) {
                $localeChildren = Page::where('locale_parent_id', $page->id)->where(function ($query) use ($previewToken) {
                    $query->where('published', true)->orWhere('preview_token', $previewToken);
                })->whereDoesntHave('childDraft', function ($query) use ($previewToken) {
                    $query->where('preview_token', $previewToken);
                })->get();

                $_pages = collect([$page, $localeChildren])->flatten();
                $_data = [
                    'locales' => $_pages->pluck('locale'),
                    'id' => $_pages->pluck('id', 'locale'),
                    'name' => $_pages->pluck('name', 'locale'),
                    'slug' => $_pages->pluck('slug', 'locale'),
                    'template' => $page->template,
                ];

                $children = Page::where('parent_id', $page->id)->where(function ($query) use ($previewToken) {
                    $query->where('published', true)->orWhere('preview_token', $previewToken);
                })->whereDoesntHave('childDraft', function ($query) use ($previewToken) {
                    $query->where('preview_token', $previewToken);
                })->get();

                if ($children->count() > 0) {
                    $_data['children'] = $formatPages($children);
                }

                $data[] = $_data;
            });
            return $data;
        };

        $parentPages = Page::whereNull('parent_id')->whereNull('locale_parent_id')->where(function ($query) use ($previewToken) {
            $query->where('published', true)->orWhere('preview_token', $previewToken);
        })->whereDoesntHave('childDraft', function ($query) use ($previewToken) {
            $query->where('preview_token', $previewToken);
        })->get();

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
                    'data' => $_regions->map(function ($_region) {
                        return [
                            'locale' => $_region->locale,
                            'data' => nova_resolve_template_model_data($_region),
                        ];
                    })->pluck('data', 'locale'),
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

    function nova_get_page($pageId, $previewToken = null)
    {
        if (empty($pageId)) return null;


        $page = Page::where(function ($query) use ($previewToken, $pageId) {
            $query->where('id', $pageId)->whereDoesntHave('childDraft', function ($query) use ($previewToken) {
                $query->where('preview_token', $previewToken);
            });
        })->orWhere(function ($query) use ($previewToken, $pageId) {
            $query->where('preview_token', $previewToken)->where('draft_parent_id', $pageId);
        })->firstOrFail();

        if ((isset($page->preview_token) && $page->preview_token !== $previewToken) || empty($page)) {
            return null;
        }

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
// nova_format_page
// ------------------------------

if (!function_exists('nova_format_page')) {
    function nova_format_page($page)
    {
        return [
            'locale' => $page->locale ?: null,
            'id' => $page->id ?: null,
            'name' => $page->name ?: null,
            'slug' => $page->slug ?: null,
            'data' => nova_resolve_template_model_data($page),
            'template' => $page->template ?: null,
        ];
    }
}


// ------------------------------
// nova_get_page_by_slug
// ------------------------------

if (!function_exists('nova_get_page_by_slug')) {

    function nova_get_page_by_slug($slug, $previewToken = null)
    {
        if (empty($slug)) return null;

        $page = Page::where('slug', $slug)->whereDoesntHave('childDraft', function ($query) use ($previewToken) {
            $query->where('preview_token', $previewToken);
        })->firstOrFail();

        if ((isset($page->preview_token) && $page->preview_token !== $previewToken) || empty($page)) {
            return null;
        }

        $data = [
            'locale' => $page->locale,
            'id' => $page->id,
            'name' => $page->name,
            'slug' => $page->slug,
            'data' => nova_resolve_template_model_data($page),
            'template' => $page->template,
        ];

        // If SEO is enabled, return SEO fields as well
        $template = collect(NovaPageManager::getPageTemplates())->first(function ($template) use ($page) {
            return $template::$name === $page->template;
        });

        // Fail silently in case template was deleted
        if (!isset($template)) return null;

        if ($template::$seo) {
            $data['seo'] = [
                'title' => $page->seo_title,
                'description' => $page->seo_description,
                'image' => $page->seo_image,
            ];
        }

        return $data;
    }
}


// ------------------------------
// nova_resolve_template_field_value
// ------------------------------

if (!function_exists('nova_resolve_template_field_value')) {
    function nova_resolve_template_field_value($field, $fieldValue, $templateModel)
    {
        return method_exists($field, 'resolveResponseValue')
            ? $field->resolveResponseValue($fieldValue, $templateModel)
            : $fieldValue;
    }
}


// ------------------------------
// nova_resolve_template_model_data
// ------------------------------

if (!function_exists('nova_resolve_template_model_data')) {
    function nova_resolve_template_model_data(TemplateModel $templateModel)
    {
        if (!isset($templateModel) || !isset($templateModel->template) || !isset($templateModel->data)) return null;

        // Find the Template class for the model
        foreach (NovaPageManager::getTemplates() as $tmpl) {
            if ($tmpl::$name === $templateModel->template) $templateClass = $tmpl;
        }

        // Fail silently is template is no longer registered
        if (!isset($templateClass)) return null;

        // Get the template's fields
        $fields = collect((new $templateClass($templateModel))->fields(request()));

        return nova_resolve_fields_data($fields, $templateModel->data, $templateModel);
    }
}


// ------------------------------
// nova_resolve_fields_data
// ------------------------------

if (!function_exists('nova_resolve_fields_data')) {
    function nova_resolve_fields_data(Collection $fields, $data, $templateModel)
    {
        $resolvedData = [];

        foreach (((array) $data) as $fieldAttribute => $fieldValue) {

            $field = $fields->first(function ($value, $key) use ($fieldAttribute) {
                return (
                    ((isset($value->attribute) && $value->attribute === $fieldAttribute)) || // Normal or flexible field
                    ((isset($value->component)) && ($value->component === 'panel') && (nova_page_manager_sanitize_panel_name($value->name) === $fieldAttribute)) // Panel
                );
            });

            if (!$field || $field instanceof Heading) {
                continue;
            } else if ($field instanceof Panel) {
                $panelAttributeName = nova_page_manager_sanitize_panel_name($field->name);
                $resolvedData[$panelAttributeName] = nova_resolve_fields_data(collect($field->data), $data->{$panelAttributeName}, $templateModel);
            } else if (isset($field->component) && $field->component === 'nova-flexible-content') {
                $resolvedData[$fieldAttribute] = collect($fieldValue)->map(function ($fieldVal) use ($field, $templateModel) {
                    $accessProtectedProperty = function ($object, $property) {
                        $reflection = new ReflectionClass($object);
                        $_property = $reflection->getProperty($property);
                        $_property->setAccessible(true);
                        return $_property->getValue($object);
                    };

                    $flexibleLayouts = $accessProtectedProperty($field, 'layouts');

                    $layout = $flexibleLayouts->first(function ($flexibleLayout) use ($fieldVal, $accessProtectedProperty) {
                        $layoutName = $accessProtectedProperty($flexibleLayout, 'name');
                        return $fieldVal->layout === $layoutName;
                    });

                    return [
                        'layout' => $accessProtectedProperty($layout, 'name'),
                        'attributes' => nova_resolve_fields_data($accessProtectedProperty($layout, 'fields'), $fieldVal->attributes, $templateModel)
                    ];
                })->toArray();
            } else {
                $resolvedData[$field->attribute] = nova_resolve_template_field_value($field, $data->{$field->attribute}, $templateModel);
            }
        }

        return $resolvedData;
    }
}


// ------------------------------
// nova_page_manager_sanitize_panel_name
// ------------------------------

if (!function_exists('nova_page_manager_sanitize_panel_name')) {
    function nova_page_manager_sanitize_panel_name($name)
    {
        return str_replace(' ', '_', strtolower($name));
    }
}
