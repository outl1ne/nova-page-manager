<?php

namespace Outl1ne\PageManager\Nova\Resources;

use Laravel\Nova\Panel;
use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Outl1ne\PageManager\Template;
use Outl1ne\PageManager\Nova\Fields\PageManagerField;

class Region extends TemplateResource
{
    public static $title = 'name';
    public static $model = null;
    public static $displayInNavigation = false;
    public static $search = ['name', 'template'];

    protected $type = 'region';



    // ------------------------------
    // Core resource setup
    // ------------------------------

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



    // ------------------------------
    // Fields
    // ------------------------------

    public function fields(Request $request)
    {
        return [
            // Template selector
            Select::make(__('novaPageManager.templateField'), 'template')
                ->options(fn () => $this->getTemplateOptions(Template::TYPE_REGION))
                ->rules('required', 'max:255')
                ->displayUsingLabels()
                ->showOnPreview(),

            // Name field
            Text::make(__('novaPageManager.nameField'), 'name')
                ->translatable(NPM::getLocales())
                ->rules('required', 'max:255')
                ->showOnPreview(),

            // Page data panel
            Panel::make(__('novaPageManager.pageFieldsPanelName'), [
                PageManagerField::make(\Outl1ne\PageManager\Template::TYPE_REGION)
                    ->withTemplate($this->template)
                    ->hideWhenCreating(),
            ])
        ];
    }
}
