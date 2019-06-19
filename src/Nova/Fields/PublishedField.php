<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;

class PublishedField extends Field 
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'published-field';

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
        parent::__construct($name, 'published', $resolveCallback);

        $this->withMeta([
            'asHtml' => true,
        ]);
        
        $this->exceptOnForms();
    }

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $childDraft = $resource->childDraft;
        $draftParent = $resource->draftParent;

        $this->withMeta([
            'childDraft' => $resource->childDraft,
            'draftParent' => $resource->draftParent
        ]);
    }


}
