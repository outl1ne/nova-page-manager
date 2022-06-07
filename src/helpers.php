<?php

use Outl1ne\PageManager\NPM;

if (!function_exists('npmGetPageByPath')) {
    function npmGetPageByPath($path)
    {
        // TODO Find page by path and return data
    }
}

if (!function_exists('npmFormatPage')) {
    function npmFormatPage($page)
    {
        if (empty($page)) return null;

        $template = NPM::getPageTemplateBySlug($page->template);
        if (empty($template)) return null;

        $request = request();

        $pageData = [
            'id' => $page->id,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
            'name' => $page->name ?: [],
            'slug' => $page->slug ?: [],
            'path' => $page->path ?: [],
            'parent_id' => $page->parent_id,
            'data' => $template->resolve($request, $page),
            'template' => $page->template ?: null,
        ];

        // if ($template::$seo) {
        //     $pageData['seo'] = [
        //         'title' => $page->seo_title,
        //         'description' => $page->seo_description,
        //         'image' => $page->seo_image,
        //     ];
        // }

        return $pageData;
    }
}
