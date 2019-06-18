<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Fields\Text;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Panel;
use OptimistDigital\NovaPageManager\Nova\Fields\RegionField;
use OptimistDigital\NovaPageManager\NovaPageManager;
use OptimistDigital\NovaLocaleField\LocaleField;
use Laravel\Nova\Fields\Heading;

class Region extends TemplateResource
{
    public static $title = 'name';
    public static $model = 'OptimistDigital\NovaPageManager\Models\Region';
    public static $displayInNavigation = false;

    protected $type = 'region';

    public function fields(Request $request)
    {
        // Get base data
        $templateFieldsAndPanels = $this->getTemplateFieldsAndPanels();

        // Create fields array
        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name')->rules('required'),
            RegionField::make('Region'),
            LocaleField::make('Locale', 'locale', 'locale_parent_id')
                ->locales(NovaPageManager::getLocales())
                ->maxLocalesOnIndex(config('nova-page-manager.max_locales_shown_on_index', 4)),
        ];

        if (count($templateFieldsAndPanels['fields']) > 0) {
            $fields[] = new Panel('Region data', array_merge(
                [Heading::make('Region data')->hideFromDetail()],
                $templateFieldsAndPanels['fields']
            ));
        }

        if (count($templateFieldsAndPanels['panels']) > 0) {
            $fields = array_merge($fields, $templateFieldsAndPanels['panels']);
        }

        return $fields;
    }

    public function title()
    {
        return $this->name;
    }
}
