<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table name
    |--------------------------------------------------------------------------
    |
    | Set a custom table for Nova Page Manager to store its data.
    |
    */

    'table' => 'nova_page_manager',


    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Register all templates (for both pages and regions) here.
    |
    */

    'templates' => [],


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

    'locales' => ['en' => 'English'],


    /*
    |--------------------------------------------------------------------------
    | Max locales shown on index
    |--------------------------------------------------------------------------
    |
    | Sets the number of locales shown on index. If the number of locales
    | exceeds the defined count, the locales will be shown only on the detail
    | view.
    |
    */

    'max_locales_shown_on_index' => 4,


    /*
    |--------------------------------------------------------------------------
    | Overwrite the page resource with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of the Page resource
    |
    */

    'page_resource' => null,


    /*
    |--------------------------------------------------------------------------
    | Overwrite the region resource with a custom implementation
    |--------------------------------------------------------------------------
    |
    | Add a custom implementation of the Region resource
    |
    */

    'region_resource' => null,


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
];
