<?php

namespace Outl1ne\PageManager\Helpers;

use Outl1ne\PageManager\NPM;

class NPMHelpers
{
    public static function getPageByPath($path)
    {
        // TODO Get page by path
    }

    public static function getPageByTemplate($templateSlug)
    {
        $page = NPM::getPageModel()::where('template', $templateSlug)->first();
        return static::formatPage($page);
    }

    public static function getPagesByTemplate($path)
    {
        // TODO Get page by path
    }

    public static function formatPage($page)
    {
        if (empty($page)) return null;

        $template = NPM::getPageTemplateBySlug($page->template);
        if (empty($template)) return null;

        $request = request();
        $templateClass = new $template['class'];

        $pageData = [
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

        return $pageData;
    }
}
