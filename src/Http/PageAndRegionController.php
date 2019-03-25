<?php

namespace OptimistDigital\NovaPageManager\Http;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use OptimistDigital\NovaPageManager\Models\Page;
use OptimistDigital\NovaPageManager\Models\Region;
use OptimistDigital\NovaPageManager\Nova\TemplateResource;
use OptimistDigital\NovaPageManager\Nova\Page as PageResource;

class PageAndRegionController extends Controller
{
    public function getPagesAndRegions(NovaRequest $request)
    {
        $pages = Page::all();
        $regions = Region::all();

        return [
            'pages' => $pages,
            'regions' => $regions
        ];
    }
}
