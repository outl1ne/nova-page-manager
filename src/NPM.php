<?php

namespace Outl1ne\NovaPageManager;

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
        return config('nova-page-manager.page_model', \Outl1ne\NovaPageManager\Models\Page::class);
    }

    public static function getRegionModel(): string
    {
        return config('nova-page-manager.region_model', \Outl1ne\NovaPageManager\Models\Region::class);
    }


    // Resources
    public static function getPageResource(): string
    {
        return config('nova-page-manager.page_resource', \Outl1ne\NovaPageManager\Nova\Resources\Page::class);
    }

    public static function getRegionResource(): string
    {
        return config('nova-page-manager.region_resource', \Outl1ne\NovaPageManager\Nova\Resources\Region::class);
    }



    // Templates
    public static function getTemplates(): array
    {
        $templates = config('nova-page-manager.templates', []);
        return array_filter($templates, fn ($template) => class_exists($template));
    }

    public static function getPageTemplates(): array
    {
        return array_filter(static::getTemplates(), fn ($template) => $template::$type === 'page');
    }

    public static function getRegionTemplates(): array
    {
        return array_filter(static::getTemplates(), fn ($template) => $template::$type === 'region');
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
    public static function getPageUrl($page)
    {
        $getPageUrl = config('nova-page-manager.page_url');
        return isset($getPageUrl) ? call_user_func($getPageUrl, $page) : null;
    }

    public static function getPagePath($page, $path)
    {
        $getPagePath = config('nova-page-manager.page_path');
        return isset($getPagePath) ? call_user_func($getPagePath, $page, $path) : $path;
    }



    // Others
    public static function getLocales(): array
    {
        $localesConfig = config('nova-page-manager.locales', ['en' => 'English']);
        if (is_callable($localesConfig)) return call_user_func($localesConfig);
        if (is_array($localesConfig)) return $localesConfig;
        return ['en' => 'English'];
    }

    public static function getCustomSeoFields(): array
    {
        $seoFields = config('nova-page-manager.seo_fields', null);
        if (is_callable($seoFields)) return call_user_func($seoFields);
        if (is_array($seoFields)) return $seoFields;
        return [];
    }
}
