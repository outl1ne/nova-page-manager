<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use OptimistDigital\NovaPageManager\Models\Page;

class ParentField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'parent-field';

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
        parent::__construct($name, 'parent_id', $resolveCallback);

        $options = [];

        Page
            ::whereNull('locale_parent_id')
            ->get()
            ->each(function ($page) use (&$options) {
                $options[$page->id] = $page->name . ' (' . $page->slug . ')';
            });

        $this->withMeta([
            'asHtml' => true,
            'options' => $options,
        ]);

        $optionKeys = array_keys($options);
        $this->rules('nullable', 'in:' . implode(',', $optionKeys));
    }

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $this->withMeta([
            'canHaveParent' => empty($resource->locale_parent_id)
        ]);
    }
}
