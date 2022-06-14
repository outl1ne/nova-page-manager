<?php

namespace Outl1ne\PageManager\Helpers;

use Outl1ne\PageManager\NPM;
use Illuminate\Support\Arr;

class NPMHelpers
{
    public static function getRegions()
    {
        return NPM::getRegionModel()::all()->map(fn ($region) => static::formatRegion($region));
    }

    public static function getPages()
    {
        return NPM::getPageModel()::all()->map(fn ($page) => static::formatPage($page));
    }

    public static function getPagesStructure()
    {
        $allPages = NPM::getPageModel()::all();

        $pageStructure = [];

        $formatAndAddChildren = function ($page) use ($allPages, &$formatAndAddChildren) {
            $formattedPage = static::formatPage($page);
            $children = $allPages->filter(fn ($sp) => $sp->parent_id === $page->id)->values();
            $formattedPage['children'] = $children->map(fn ($page) => $formatAndAddChildren($page));
            return $formattedPage;
        };

        $rootPages = $allPages->filter(fn ($page) => empty($page->parent_id))->values();
        $rootPages->each(function ($page) use (&$pageStructure, $formatAndAddChildren) {
            $pageStructure[] = $formatAndAddChildren($page);
        });

        return $pageStructure;
    }

    public static function getPageByPath($path = '')
    {
        if (!$path) return null;
        $path = $path !== '/' ? rtrim($path, '/') : $path;
        $pages = NPM::getPageModel()::all();
        $originalPathSlugs = explode('/', $path);

        foreach (NPM::getLocales() as $localeSlug => $locale) {
            foreach ($pages as $page) {
                if (!array_key_exists($localeSlug, $page->path)) continue;
                $pagePath = preg_replace('/:[^\/]*/', ':', $page->path[$localeSlug]); // change all ':slug' to ':'
                $pagePathSlugs = explode('/', $pagePath);

                if (count($originalPathSlugs) !== count($pagePathSlugs)) continue;
                $pathSlugs = Arr::map(explode('/', $path), function ($slug, $i) use ($pagePathSlugs) {
                    return $pagePathSlugs[$i] === ':' ? ':' : $slug;
                });

                if ($pagePath === join('/', $pathSlugs)) {
                    return NPMHelpers::formatPage($page);
                }
            }
        }

        return null;
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
