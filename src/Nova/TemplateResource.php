<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use OptimistDigital\NovaPageManager\NovaPageManager;
use Laravel\Nova\Panel;

class TemplateResource extends Resource
{
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $templatesArray = NovaPageManager::getTemplates();
        $templates = [];
        foreach ($templatesArray as $tmpl) {
            $templates[$tmpl::$name] = $tmpl::$name;
        }

        if (isset($this->template)) {
            foreach ($templatesArray as $tmpl) {
                if ($tmpl::$name == $this->template) $templateClass = new $tmpl;
            }
        }

        $templateFields = isset($templateClass) ? $templateClass->_getTemplateFields($request) : [];

        return [
            ID::make()->sortable(),
            Text::make('Name', 'name'),
            Text::make('Slug', 'slug'),
            Select::make('Locale', 'locale')->options([
                'en_US' => 'English'
            ]),
            Select::make('Template', 'template')->options($templates),

            new Panel('Page information', $templateFields)
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
