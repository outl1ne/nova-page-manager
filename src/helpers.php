<?php

use OptimistDigital\NovaPageManager\Models\Page;
use OptimistDigital\NovaPageManager\Models\Region;
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
