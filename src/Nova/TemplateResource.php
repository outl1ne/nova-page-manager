<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Heading;
use OptimistDigital\NovaPageManager\NovaPageManager;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\FieldCollection;
use OptimistDigital\NovaPageManager\Nova\Fields\TranslationsField;
use OptimistDigital\NovaPageManager\Nova\Fields\LocaleField;
use OptimistDigital\NovaPageManager\Nova\Fields\TemplateField;
use OptimistDigital\NovaPageManager\Nova\Filters\TemplateLocaleFilter;
use OptimistDigital\NovaPageManager\Nova\Filters\TemplateChildrenFilter;

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
            return ($template::$type === $this->type);
        });

        // If is existing model, find correct class
        if (isset($this->template)) {
            foreach ($templatesArray as $tmpl) {
                if ($tmpl::$name == $this->template) $templateClass = new $tmpl;
            }
        }

        $templateFields = isset($templateClass) ? $templateClass->_getTemplateFields($request) : [];

        $table = config('nova-page-manager.table', 'nova-page-manager');

        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name')->rules('required'),
            Text::make('Slug', 'slug')->creationRules('required', "unique:{$table},slug")
                                      ->updateRules('required', "unique:{$table},slug,{{resourceId}}"),

            LocaleField::make('Locale', 'locale'),
            TemplateField::make('Template', 'template'),
            TranslationsField::make('Translations')
        ];

        if (isset($templateClass) && $templateClass::$seo) $fields[] = new Panel('SEO', $this->getSeoFields());

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
        return [
            new TemplateLocaleFilter,
            new TemplateChildrenFilter
        ];
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
