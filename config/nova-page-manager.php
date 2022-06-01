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
    | Overwrite the page resource with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of the Page resource.
    |
    | Return false if you want to disable the Page resource
    | and hide it from the sidebar.
    |
    */

    'page_resource' => \Outl1ne\PageManager\Nova\Resources\Page::class,


    /*
    |--------------------------------------------------------------------------
    | Overwrite the page model with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of the Page model.
    |
    */

    'page_model' => \Outl1ne\PageManager\Models\Page::class,


    /*
    |--------------------------------------------------------------------------
    | Overwrite the region resource with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of the Region resource.
    |
    | Return false if you want to disable the Region resource
    | and hide it from the sidebar.
    |
    */

    'region_resource' => \Outl1ne\PageManager\Nova\Resources\Region::class,


    /*
    |--------------------------------------------------------------------------
    | Overwrite the region model with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of the Region model.
    |
    */

    'region_model' => \Outl1ne\PageManager\Models\Region::class,

    /*
    |--------------------------------------------------------------------------
    | Overwrite seo fields with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of seo fields.
    |
    | When $seo is assigned as true in template class this custom array of
    | fields will be displayed in resource view instead of the default one.
    |
    */

    'seo_fields' => null,


    /*
    |--------------------------------------------------------------------------
    | Page URL
    |--------------------------------------------------------------------------
    |
    | If a closure is specified, a link to the page is shown next to
    | the page slug. The closure accepts a Page model as a paramater.
    |
    | Set to `null` if the link should not be displayed.
    |
    | Closure example:
    | function (Page $page) {
    |   return env('FRONTEND_URL') . '/' . $page->path;
    | }
    |
    */

    'page_url' => null,


    /*
    |--------------------------------------------------------------------------
    | Page path
    |--------------------------------------------------------------------------
    |
    | If a closure is specified, you can modify the page path before
    | it's finalized.
    |
    | The closure will be called with parameters (Page $page, $path).
    |
    | An example usecase is when you want to add a locale prefix to non-default
    | locale pages.
    |
    | Set to `null` if you do not wish to modify page paths.
    |
    */

    'page_path' => null,
];
