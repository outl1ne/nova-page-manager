<?php

namespace Outl1ne\PageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;

class PageManager extends Tool
{
    protected $seoFieldsConfig = null;

    public function boot()
    {
       //
    }

    public function menu(Request $request)
    {
        $pageResource = NPM::getPageResource();
        $pagesMenuItem = NPM::pagesEnabled()
            ? MenuItem::make($pageResource::label(), '/resources/' . $pageResource::uriKey())
            : null;

        $regionResource = NPM::getRegionResource();
        $regionsMenuItem = NPM::regionsEnabled()
            ? MenuItem::make($regionResource::label(), '/resources/' . $regionResource::uriKey())
            : null;

        return MenuSection::make(__('novaPageManager.sidebarTitle'), array_filter([
            $pagesMenuItem,
            $regionsMenuItem,
        ]))
            ->icon('newspaper')
            ->collapsable();
    }

    public function withSeoFields($seoFields)
    {
        $this->seoFieldsConfig = $seoFields;
        return $this;
    }

    public function getSeoFieldsConfig()
    {
        return $this->seoFieldsConfig ?? config('nova-page-manager.page_seo_fields', []);
    }
}
