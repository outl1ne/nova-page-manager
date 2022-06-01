<?php

namespace Outl1ne\PageManager\Http;

use Outl1ne\PageManager\NPM;
use Illuminate\Routing\Controller;
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
