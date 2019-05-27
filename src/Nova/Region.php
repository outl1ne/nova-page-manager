<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Fields\Text;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Panel;
use OptimistDigital\NovaPageManager\Nova\Fields\LocaleParentField;
use OptimistDigital\NovaPageManager\Nova\Fields\LocaleField;
use OptimistDigital\NovaPageManager\Nova\Fields\RegionField;
use OptimistDigital\NovaPageManager\NovaPageManager;

class Region extends TemplateResource
{
    public static $title = 'name';
    public static $model = 'OptimistDigital\NovaPageManager\Models\Region';
    public static $displayInNavigation = false;

    protected $type = 'region';

    public function fields(Request $request)
    {
        // Get base data
        $templateFields = $this->getTemplateFields();
        $localeParentField = LocaleParentField::make('Translations');

        if (count(NovaPageManager::getLocales()) > 2) {
            $localeParentField = $localeParentField->hideFromIndex();
        }

        // Create fields array
        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name')->rules('required'),
            RegionField::make('Region'),
            LocaleField::make('Locale', 'locale'),
            $localeParentField,
            new Panel('Region data', $templateFields),
        ];

        return $fields;
    }

    public function title()
    {
        return $this->name;
    }
}
