<?php

namespace Outl1ne\PageManager\Nova\Resources;

use Laravel\Nova\Panel;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Text;

class Region extends TemplateResource
{
    public static $title = 'name';
    public static $model = null;
    public static $displayInNavigation = false;
    public static $search = ['name', 'template'];

    protected $type = 'region';

    public function __construct($resource)
    {
        self::$model = NPM::getRegionModel();
        parent::__construct($resource);
    }

    public static function newModel()
    {
        $model = empty(self::$model) ? NPM::getRegionModel() : self::$model;
        return new $model;
    }

    public function fields(Request $request)
    {
        // Get base data
        $templateFieldsAndPanels = $this->getTemplateFieldsAndPanels();

        // Create fields array
        $fields = [
            ID::make()->sortable(),
            Text::make(__('novaPageManager.name'), 'name')->rules('required'),
        ];

        if (count($templateFieldsAndPanels['fields']) > 0) {
            $fields[] = new Panel(__('novaPageManager.regionData'), $templateFieldsAndPanels['fields']);
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

    public static function label()
    {
        return __('novaPageManager.regionResourceLabel');
    }

    public static function singularLabel()
    {
        return __('novaPageManager.regionResourceSingularLabel');
    }
}
