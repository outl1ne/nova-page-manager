<?php

namespace Outl1ne\PageManager;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Outl1ne\PageManager\Commands\NPMTemplateCommand;
use Outl1ne\NovaTranslationsLoader\LoadsNovaTranslations;

class NPMServiceProvider extends ServiceProvider
{
    use LoadsNovaTranslations;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nova-page-manager.php', 'nova-page-manager');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load all data
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslations(__DIR__ . '/../lang', 'nova-page-manager');

        // Publish migrations and config
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'nova-page-manager-migrations');

        $this->publishes([
            __DIR__ . '/../config/nova-page-manager.php' => config_path('nova-page-manager.php'),
        ], 'config');

        // Register resources
        Nova::resources(array_filter([
            NPM::getPageResource(),
            NPM::getRegionResource(),
        ]));

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                NPMTemplateCommand::class
            ]);
        }

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        if ($this->app->routesAreCached()) return;

        Route::middleware(['nova', \Outl1ne\PageManager\Http\Middleware\AuthorizeMiddleware::class])
            ->group(__DIR__ . '/../routes/api.php');
    }
}
