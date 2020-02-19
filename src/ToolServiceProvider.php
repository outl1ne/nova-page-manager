<?php

namespace OptimistDigital\NovaPageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Fields\Field;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use OptimistDigital\NovaPageManager\Nova\Page;
use OptimistDigital\NovaPageManager\Nova\Region;
use OptimistDigital\NovaPageManager\FieldResponseMixin;
use OptimistDigital\NovaPageManager\Commands\CreateTemplate;
use OptimistDigital\NovaPageManager\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-page-manager');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'nova-page-manager-migrations');

        $this->publishes([
            __DIR__ . '/../config/nova-page-manager.php' => config_path('nova-page-manager.php'),
        ], 'config');

        // Register resources
        $pageResource = config('nova-page-manager.page_resource') ?: Page::class;
        $regionResource = config('nova-page-manager.region_resource') ?: Region::class;

        Nova::resources([
            $pageResource,
            $regionResource,
        ]);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTemplate::class
            ]);
        }

        // Custom validation
        Validator::extend('alpha_dash_or_slash', function ($attribute, $value, $parameters, $validator) {
            if (!is_string($value) && !is_numeric($value)) return false;
            if ($value === '/') return true;
            return preg_match('/^[\pL\pM\pN_-]+$/u', $value) > 0;
        }, 'Field must be alphanumeric with dashes or underscores or a single slash.');

        Field::mixin(new FieldResponseMixin);
    }
}
