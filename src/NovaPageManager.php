<?php

namespace Outl1ne\NovaPageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaPageManager extends Tool
{
    public function boot()
    {
        Nova::script('nova-page-manager', __DIR__ . '/../dist/js/entry.js');
        Nova::style('nova-page-manager', __DIR__ . '/../dist/css/entry.css');
    }
}
