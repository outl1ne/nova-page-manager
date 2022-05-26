<?php

namespace Outl1ne\NovaPageManager;

use Laravel\Nova\Nova;
use Laravel\Nova\Fields\Field;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Outl1ne\NovaPageManager\FieldResponseMixin;
use Outl1ne\NovaPageManager\Commands\CreateTemplate;
use Outl1ne\NovaTranslationsLoaderPHP\LoadsNovaTranslations;

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
        $this->app->singleton(NPMCache::class, fn () => new NPMCache);
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-page-manager');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslations(__DIR__ . '/../resources/lang', 'nova-page-manager', true);

        // Publish migrations and config
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'nova-page-manager-migrations');

        $this->publishes([
            __DIR__ . '/../config/nova-page-manager.php' => config_path('nova-page-manager.php'),
        ], 'config');

        // Register resources
        Nova::resources([
            NPM::getPageResource(),
            NPM::getRegionResource(),
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
