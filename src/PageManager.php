<?php

namespace Outl1ne\PageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;

class PageManager extends Tool
{
    public function boot()
    {
        Nova::script('nova-page-manager', __DIR__ . '/../dist/js/entry.js');
        Nova::style('nova-page-manager', __DIR__ . '/../dist/css/entry.css');
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
}
