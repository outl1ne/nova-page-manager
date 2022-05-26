<?php

namespace Outl1ne\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use Outl1ne\NovaPageManager\NPM;

class TemplateField extends Field
{
    public $component = 'template-field';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $resourceName = rtrim(request()->route('resource'), 's');

        $this->withMeta([
            'asHtml' => true,
            'templates' => collect(NPM::getTemplates())
                ->filter(fn ($template) => $template::$type === $resourceName)
                ->map(function ($template) {
                    return [
                        'label' => $template::$name,
                        'value' => $template::$name
                    ];
                }),
            'resourceTemplates' => collect(NPM::getPageModel()::all(), NPM::getRegionModel()::all())->flatten()->pluck('template', 'id')
        ]);

        $templates = array_map(fn ($template) => $template::$name, NPM::getTemplates());
        $this->rules('required', 'in:' . implode(',', $templates));
    }
}
