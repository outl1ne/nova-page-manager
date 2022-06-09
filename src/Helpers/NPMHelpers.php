<?php

namespace Outl1ne\PageManager\Helpers;

use Outl1ne\PageManager\NPM;

class NPMHelpers
{
    public static function getRegions()
    {
        return NPM::getRegionModel()::all()->map(fn ($region) => static::formatRegion($region));
    }

    public static function getPageByPath($path)
    {
        // TODO Get page by path
    }

    public static function getPageByTemplate($templateSlug)
    {
        $page = NPM::getPageModel()::where('template', $templateSlug)->first();
        return static::formatPage($page);
    }

    public static function getPagesByTemplate($templateSlug)
    {
        $pages = NPM::getPageModel()::where('template', $templateSlug)->get();
        return $pages->map(fn ($page) => static::formatPage($page))->toArray();
    }

    public static function formatPage($page)
    {
        if (empty($page)) return null;

        $template = NPM::getPageTemplateBySlug($page->template);
        if (empty($template)) return null;

        $templateClass = new $template['class'];

        return [
            'id' => $page->id,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
            'name' => $page->name ?: [],
            'slug' => $page->slug ?: [],
            'path' => $page->path ?: [],
            'parent_id' => $page->parent_id,
            'data' => $templateClass->resolve($page),
            'template' => $page->template ?: null,
        ];
    }

    public static function formatRegion($region)
    {
        if (empty($region)) return null;

        $template = NPM::getRegionTemplateBySlug($region->template);
        if (empty($template)) return null;

        $templateClass = new $template['class'];

        return [
            'id' => $region->id,
            'created_at' => $region->created_at,
            'updated_at' => $region->updated_at,
            'name' => $region->name ?: [],
            'data' => $templateClass->resolve($region),
            'template' => $region->template ?: null,
        ];
    }
}
