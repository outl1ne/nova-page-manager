<?php

namespace OptimistDigital\NovaPageManager\Http;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use OptimistDigital\NovaPageManager\NovaPageManager;

class PageAndRegionController extends Controller
{
    public function getPagesAndRegions(NovaRequest $request)
    {
        $pages = NovaPageManager::getPageModel()::all();
        $regions = NovaPageManager::getRegionModel()::all();

        return [
            'pages' => $pages,
            'regions' => $regions
        ];
    }
}
