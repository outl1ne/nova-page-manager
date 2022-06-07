<?php

namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Outl1ne\PageManager\Template;

class :className extends Template
{
    // Name displayed in CMS
    public function name(Request $request) {
        return static::class;
    }

    // Fields displayed in CMS
    public function fields(Request $request): array
    {
        return [];
    }

    // Resolve data for serialization
    public function resolve(Request $request, $page): array {
        // Modify data as you please (ie turn ID-s into models)
        return $page->data;
    }

    // Optional suffix to the route (ie {blogPostName})
    public function pathSuffix(Request $request) {
        return null;
    }
}
