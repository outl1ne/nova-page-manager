<?php

use OptimistDigital\NovaPageManager\Models\Page;
use Illuminate\Support\Collection;

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
