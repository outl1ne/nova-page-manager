<?php

namespace OptimistDigital\NovaPageManager\Http;

use Illuminate\Routing\Controller;
use OptimistDigital\NovaPageManager\NPM;
use Laravel\Nova\Http\Requests\NovaRequest;

class PageAndRegionController extends Controller
{
    public function getPagesAndRegions(NovaRequest $request)
    {
        $pages = NPM::getPageModel()::all();
        $regions = NPM::getRegionModel()::all();

        return [
            'pages' => $pages,
            'regions' => $regions
        ];
    }
}
