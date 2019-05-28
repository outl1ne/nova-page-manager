<?php

namespace OptimistDigital\NovaPageManager;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use OptimistDigital\NovaPageManager\Http\Middleware\Authorize;
use OptimistDigital\NovaPageManager\Nova\Page;
use OptimistDigital\NovaPageManager\Nova\Region;
use OptimistDigital\NovaPageManager\Commands\CreateTemplate;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-page-manager');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../config/nova-page-manager.php' => config_path('nova-page-manager.php'),
        ], 'config');

        $this->app->booted(function () {
            $this->routes();
        });

        $pageResource = config('nova-page-manager.page_resource') ?: Page::class;
        $regionResource = config('nova-page-manager.region_resource') ?: Region::class;

        Nova::resources([
            $pageResource,
            $regionResource,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTemplate::class
            ]);
        }
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/nova-page-manager')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
