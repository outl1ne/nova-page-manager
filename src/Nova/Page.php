<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;

class Page extends TemplateResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'OptimistDigital\NovaPageManager\Models\Page';

    public $type = 'page';
}
