<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table names
    |--------------------------------------------------------------------------
    |
    | Set table names for the pages and regions tables.
    |
    */

    'pages_table' => 'pages',
    'regions_table' => 'regions',

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Register all templates (for both pages and regions) here.
    |
    */

    'templates' => [
        'pages' => [
            // 'home-page' => [
            //     'class' => '\App\Nova\Templates\HomePageTemplate',
            //     'unique' => true,
            // ],
        ],
        'regions' => [
            // 'header' => [
            //     'class' => '\App\Nova\Templates\HeaderRegionTemplate',
            //     'unique' => true,
            // ],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | Set all the available locales as [key => name] pairs.
    |
    | For example ['en_US' => 'English'].
    |
    */

    'locales' => ['en' => 'English', 'et' => 'Estonian'],



    /*
    |--------------------------------------------------------------------------
    | Resource and model overrides
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of Page and/or Region models/resources.
    |
    | Return false for any resource if you want to disable it
    | and hide the item from the navigation sidebar.
    |
    */

    'region_model' => \Outl1ne\PageManager\Models\Region::class,
    'region_resource' => \Outl1ne\PageManager\Nova\Resources\Region::class,
    'page_model' => \Outl1ne\PageManager\Models\Page::class,
    'page_resource' => \Outl1ne\PageManager\Nova\Resources\Page::class,



    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Define the base URL for your pages. Can be a string (ie https://webshop.com)
    | or a closure.
    |
    | If a closure is specified, the function is called with the $page as a
    | parameter. For example: fn($page) => config('app.url') . $page->path;
    |
    */

    'base_url' => null,
];
