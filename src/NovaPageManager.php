<?php

namespace OptimistDigital\NovaPageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaPageManager extends Tool
{
    public function boot()
    {
        Nova::script('nova-page-manager-script', __DIR__ . '/../dist/js/nova-page-manager-resources.js');
    }

    public function renderNavigation()
    {
        return view('nova-page-manager::navigation');
    }
}
