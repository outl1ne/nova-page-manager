<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use OptimistDigital\NovaPageManager\NovaPageManager;

class LocaleField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'locale-field';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'asHtml' => true,
            'locales' => collect(NovaPageManager::getLocales())->map(function ($label, $value) {
                return [
                    'label' => $label,
                    'value' => $value
                ];
            })
        ]);

        $locales = array_keys(NovaPageManager::getLocales());
        $this->rules('required', 'in:' . implode(',', $locales));
    }
}
