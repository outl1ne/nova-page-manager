<?php

namespace Outl1ne\PageManager\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Outl1ne\PageManager\NPM;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

    public static function getPagesStructure($flat = false, $keys = null)
    {
        $model = NPM::getPageModel();
        $query = $model::query();

        if (isset($keys)) {
            $query->select(array_merge(
                array_values(array_replace(array_combine($keys, $keys), ['path' => 'slug'])),
                ['id', 'parent_id', 'template'] // Mandatory keys
            ));
        }

        $pages = $query->orderBy(DB::raw('parent_id IS NULL, parent_id'), 'desc')->get();
        $pagesById = $pages->keyBy('id');
        $pageChildrenMap = array_fill_keys($pagesById->keys()->toArray(), []);
        $structure = [];


        $formatPageForStructure = function ($page) use (
            $flat,
            $keys,
            &$pageChildrenMap,
            &$formatPageForStructure,
            &$structure,
        ) {
            $children = array_map(fn ($child) => $formatPageForStructure($child), $pageChildrenMap[$page->id]);
            $formattedPage = [];


            if (isset($keys)) {
                $formattedPage = $page->only($keys);
                $template = NPM::getPageTemplateBySlug($page->template);
                $templateClass = new $template['class'];

                if (in_array('slug', $keys)) $formattedPage['slug'] = $page->getTranslations('slug') ?: [];
                if (in_array('name', $keys)) $formattedPage['name'] = $page->getTranslations('name') ?: [];

                if (isset($template)) {
                    if (in_array('data', $keys)) $formattedPage['data'] = $templateClass->resolve($page, []);
                    if (in_array('seo', $keys)) $formattedPage['seo'] = static::formatSeo($page);
                }
            } else {
                // Fallback to default formatting
                $formattedPage = static::formatPage($page);
            }

            if ($flat) {
                array_push($structure, $formattedPage);
                return;
            }

            $formattedPage['children'] = $children;
            if (!empty($page->parent_id)) return $formattedPage;
            array_push($structure, $formattedPage);
        };


        foreach ($pages as $page) {
            if (!empty($page->parent_id)) {
                $page->setRelation('parent', $pagesById[$page->parent_id]);
                $pageChildrenMap[$page->parent_id][] = $page;

                // We want root pages only.
                continue;
            }

            $formatPageForStructure($page);
        }


        return $structure;
    }

    protected static function getParams($path, $pagePath)
    {
        $pageSlugs = explode('/', $pagePath);
        $params = [];

        foreach (explode('/', $path) as $i => $slug) {
            if (Str::contains($pageSlugs[$i], [':', '{', '}'])) {
                $paramName = Str::replace([':', '{', '}'], '', $pageSlugs[$i]);
                $params[$paramName] = $slug;
            }
        }

        return $params;
    }

    public static function getPageByPath($path = '')
    {
        if (!$path) {
            return null;
        }

        $path = $path !== '/' ? rtrim($path, '/') : $path;
        $model = NPM::getPageModel();
        $pages = $model::select(['id', 'slug', 'template', 'parent_id'])->get();
        $originalPathSlugs = explode('/', $path);

        foreach (NPM::getLocales() as $localeSlug => $locale) {
            foreach ($pages as $page) {
                if (!array_key_exists($localeSlug, $page->path)) continue;
                $pagePath = preg_replace('/(:[^\/]+)|({[^}\/]+})/', ':', $page->path[$localeSlug]); // change all ':slug' to ':'
                $pagePathSlugs = explode('/', $pagePath);

                if (count($originalPathSlugs) !== count($pagePathSlugs)) continue;

                $explodedPath = explode('/', $path);
                $pathSlugs = array_map(
                    fn ($slug, $i) => $pagePathSlugs[$i] === ':' ? ':' : $slug,
                    $explodedPath,
                    array_keys($explodedPath)
                );

                if ($pagePath === join('/', $pathSlugs)) {
                    $page = $model::find($page->id);
                    $params = self::getParams($path, $page->path[$localeSlug]);
                    return self::formatPage($page, $params);
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

    public static function formatPage($page, $params = [])
    {
        if (empty($page)) return null;

        $template = NPM::getPageTemplateBySlug($page->template);
        if (empty($template)) return null;

        $seo = static::formatSeo($page);
        $templateClass = new $template['class'];

        return array_merge([
            'id' => $page->id,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
            'name' => $page->getTranslations('name') ?: [],
            'slug' => $page->getTranslations('slug') ?: [],
            'path' => $page->path ?: [],
            'parent_id' => $page->parent_id,
            'data' => $templateClass->resolve($page, $params),
            'template' => $page->template ?: null,
        ], $seo);
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
            'data' => $templateClass->resolve($region, []),
            'template' => $region->template ?: null,
        ];
    }

    protected static function formatSeo($page)
    {
        $seoConfig = config('nova-page-manager.page_seo_fields', false);
        $seoData = $page->seo ?? [];

        if (!$seoConfig) {
            return [];
        }

        if (is_callable($seoConfig)) {
            return ['seo' => $seoData];
        }

        return ['seo' => array_map(function ($localeSeo) {
            if (isset($localeSeo['image'])) {
                $localeSeo['image'] = Storage::disk('public')->url($localeSeo['image']);
            }

            return $localeSeo;
        }, $seoData)];
    }
}
