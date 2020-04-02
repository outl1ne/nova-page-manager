<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use OptimistDigital\NovaLocaleField\LocaleField;
use OptimistDigital\NovaPageManager\Nova\Fields\RegionField;
use OptimistDigital\NovaPageManager\NovaPageManager;

class Region extends TemplateResource
{
    public static $title = 'name';
    public static $model = 'OptimistDigital\NovaPageManager\Models\Region';
    public static $displayInNavigation = false;
    public static $search = ['name', 'template'];

    protected $type = 'region';

    public function fields(Request $request)
    {
        // Get base data
        $templateFieldsAndPanels = $this->getTemplateFieldsAndPanels();

        // Create fields array
        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name')->rules('required'),
            RegionField::make('Region')->sortable(),
            LocaleField::make('Locale', 'locale', 'locale_parent_id')
                ->locales(NovaPageManager::getLocales())
                ->maxLocalesOnIndex(config('nova-page-manager.max_locales_shown_on_index', 4)),
        ];

        if (count($templateFieldsAndPanels['fields']) > 0) {
            $fields[] = new Panel('Region data', $templateFieldsAndPanels['fields']);
        }

        if (NovaPageManager::hasNovaLang())
            $fields[] = \OptimistDigital\NovaLang\NovaLangField\NovaLangField::make('Locale', 'locale', 'locale_parent_id')->onlyOnForms();

        if (count($templateFieldsAndPanels['panels']) > 0) {
            $fields = array_merge($fields, $templateFieldsAndPanels['panels']);
        }

        return $fields;
    }

    public function title()
    {
        return $this->name;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (NovaPageManager::hasNovaLang()) {
            $localeColumn = NovaPageManager::getRegionsTableName() . '.locale';
            $query->where(function ($subQuery) use ($localeColumn) {
                $subQuery->where($localeColumn, nova_lang_get_active_locale())
                    ->orWhereNotIn($localeColumn, array_keys(nova_lang_get_all_locales()));
            });
        }

        return $query;
    }
}
