<?php

namespace OptimistDigital\NovaPageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaPageManager extends Tool
{
    private static $templates = [];
    private static $locales = [];

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('nova-page-manager', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-page-manager', __DIR__ . '/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('nova-page-manager::navigation');
    }

    public static function configure(array $data = [])
    {
        self::$templates = $data['templates'] ?: [];
        self::$locales = $data['locales'] ?: ['en_US' => 'English'];
    }

    public static function getTemplates(): array
    {
        return self::$templates;
    }

    public static function getLocales(): array
    {
        return self::$locales;
    }
}
