<?php

namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Outl1ne\PageManager\Template;

class :className extends Template
{
    public function name(Request $request) {
        return static::class;
    }

    public function fields(Request $request): array
    {
        return [];
    }

    public function metaData(Request $request): array
    {
        return [];
    }

    public function pathSuffix(Request $request) {
        return null;
    }
}
