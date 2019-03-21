<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Heading;
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
        // Filter 'page' and 'region' templates
        $templatesArray = array_filter(NovaPageManager::getTemplates(), function ($template) {
            if (!class_exists($template)) return false;
            return ($template::$type === $this->type);
        });

        // Create properly formatted array
        $templates = [];
        foreach ($templatesArray as $tmpl) {
            $templates[$tmpl::$name] = $tmpl::$name;
        }

        // If is existing model, find correct class
        if (isset($this->template)) {
            foreach ($templatesArray as $tmpl) {
                if ($tmpl::$name == $this->template) $templateClass = new $tmpl;
            }
        }

        $templateFields = isset($templateClass) ? $templateClass->_getTemplateFields($request) : [];

        $locales = NovaPageManager::getLocales();

        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name'),
            Text::make('Slug', 'slug'),

            Select::make('Locale', 'locale')
                ->options($locales)
                ->hideWhenUpdating(),
            Text::make('Locale', 'locale')
                ->withMeta(['extraAttributes' => [
                    'readonly' => true
                ]])
                ->hideWhenCreating()
                ->hideFromIndex()
                ->hideFromDetail(),

            Select::make('Template', 'template')
                ->options($templates)
                ->hideWhenUpdating(),
            Text::make('Template', 'template')
                ->withMeta(['extraAttributes' => [
                    'readonly' => true
                ]])
                ->hideWhenCreating()
                ->hideFromIndex()
                ->hideFromDetail()
        ];

        if ($templateClass::$seo) $fields[] = new Panel('SEO', $this->getSeoFields());

        $fields[] = new Panel('Page information', $templateFields);

        return $fields;
    }

    protected function getSeoFields()
    {
        return [
            Heading::make('SEO')->hideFromIndex()->hideWhenCreating()->hideFromDetail(),
            Text::make('SEO Title', 'seo_title')->hideFromIndex()->hideWhenCreating(),
            Text::make('SEO Description', 'seo_description')->hideFromIndex()->hideWhenCreating(),
            Image::make('SEO Image', 'seo_image')->hideFromIndex()->hideWhenCreating()
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
