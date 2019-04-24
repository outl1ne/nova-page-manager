<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Fields\Text;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Panel;
use OptimistDigital\NovaPageManager\Nova\Fields\LocaleParentField;
use OptimistDigital\NovaPageManager\Nova\Fields\LocaleField;
use OptimistDigital\NovaPageManager\Nova\Fields\RegionField;
use OptimistDigital\NovaPageManager\Models\Region as RegionModel;

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

        // Create fields array
        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name')->rules('required'),
            RegionField::make('Region'),
            LocaleField::make('Locale', 'locale'),
            LocaleParentField::make('Translations'),
            new Panel('Region data', $templateFields),
        ];

        return $fields;
    }

    public function title()
    {
        return $this->name;
    }
}
