<?php

namespace Outl1ne\PageManager;

use Laravel\Nova\Nova;
use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Textarea;
use Illuminate\Support\Facades\Log;

class NPM
{
    // Table names
    public static function getPagesTableName(): string
    {
        return config('nova-page-manager.pages_table', 'pages');
    }

    public static function getRegionsTableName(): string
    {
        return config('nova-page-manager.regions_table', 'regions');
    }



    // Models
    public static function getPageModel(): string
    {
        return config('nova-page-manager.page_model', \Outl1ne\PageManager\Models\Page::class);
    }

    public static function getRegionModel(): string
    {
        return config('nova-page-manager.region_model', \Outl1ne\PageManager\Models\Region::class);
    }


    // Resources
    public static function getPageResource(): string|null
    {
        if (!static::getPageModel()) return null;
        return config('nova-page-manager.page_resource', \Outl1ne\PageManager\Nova\Resources\Page::class);
    }

    public static function getRegionResource(): string
    {
        if (!static::getRegionModel()) return null;
        return config('nova-page-manager.region_resource', \Outl1ne\PageManager\Nova\Resources\Region::class);
    }


    // Templates
    public static function getTemplates(): array
    {
        $pageTemplates = config('nova-page-manager.templates.pages', []);
        $regionTemplates = config('nova-page-manager.templates.regions', []);

        $filterTemplates = function ($templates) {
            // Move 'key' aka 'slug' into the array itself for easy access
            $mappedTemplates = array_map(fn($t, $s) => array_merge($t, ['slug' => $s]), $templates, array_keys($templates));
            $mappedTemplates = array_combine(array_keys($templates), $mappedTemplates);

            return array_filter($mappedTemplates, function ($template) {
                if (isset($template['class']) && class_exists($template['class'])) return true;

                Log::warning("[Nova Page Manager] Received invalid configuration for template entry: " . json_encode($template));
                return false;
            });
        };

        return [
            'pages' => $filterTemplates($pageTemplates),
            'regions' => $filterTemplates($regionTemplates),
        ];
    }

    public static function getPageTemplates(): array
    {
        return static::getTemplates()['pages'];
    }

    public static function getRegionTemplates(): array
    {
        return static::getTemplates()['regions'];
    }

    public static function getPageTemplateByClass($className)
    {
        return Arr::first(static::getPageTemplates(), fn($template) => $template['class'] === $className);
    }

    public static function getRegionTemplateByClass($className)
    {
        return Arr::first(static::getRegionTemplates(), fn($template) => $template['class'] === $className);
    }

    public static function getPageTemplateBySlug($templateSlug)
    {
        return Arr::first(static::getPageTemplates(), fn($template) => $template['slug'] === $templateSlug);
    }

    public static function getRegionTemplateBySlug($templateSlug)
    {
        return Arr::first(static::getRegionTemplates(), fn($template) => $template['slug'] === $templateSlug);
    }

    public static function getTemplateClassType($templateClass)
    {
        $isPage = !!static::getPageTemplateByClass($templateClass);
        $isRegion = !!static::getRegionTemplateByClass($templateClass);
        return $isPage ? Template::TYPE_PAGE : ($isRegion ? Template::TYPE_REGION : null);
    }


    // Enabled states
    public static function pagesEnabled(): bool
    {
        return config('nova-page-manager.page_resource') !== false;
    }

    public static function regionsEnabled(): bool
    {
        return config('nova-page-manager.region_resource') !== false;
    }


    // Page URL generation
    public static function getBaseUrl($page = null)
    {
        $baseUrl = config('nova-page-manager.base_url');
        if (empty($baseUrl)) return null;
        if (is_callable($baseUrl)) return call_user_func($baseUrl, $page);
        if (empty($page)) return $baseUrl;

        // Create full URL myself
        $baseUrl = rtrim($baseUrl, '/');

        $paths = $page->path;
        $fullUrls = [];
        foreach ($paths as $localeKey => $path) {
            $fullUrls[$localeKey] = "{$baseUrl}{$path}";
        }
        return $fullUrls;
    }


    // Others
    public static function getLocales(): array
    {
        $localesConfig = config('nova-page-manager.locales', ['en' => 'English']);
        if (is_callable($localesConfig)) return call_user_func($localesConfig);
        if (is_array($localesConfig)) return $localesConfig;
        return ['en' => 'English'];
    }

    public static function getSeoFields()
    {
        $seoConfig = static::getTool()->getSeoFieldsConfig();
        if (is_callable($seoConfig)) return call_user_func($seoConfig);
        if (is_array($seoConfig)) return $seoConfig;

        if ($seoConfig === true) {
            return [
                Text::make(__('novaPageManager.seoTitle'), 'title'),
                Textarea::make(__('novaPageManager.seoDescription'), 'description'),
                // Image::make(__('novaPageManager.seoImage'), 'image'),
            ];
        }

        return null;
    }

    private static function getTool(): PageManager
    {
        $tool = collect(Nova::registeredTools())->first(fn($tool) => $tool instanceof PageManager);
        return $tool ?? new PageManager();
    }
}
