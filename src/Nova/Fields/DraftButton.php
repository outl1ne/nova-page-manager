<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;

class DraftButton extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'draft-button';

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
        ]);

        $this->hideFromIndex();
    }

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $this->withMeta([
            'childDraft' => $resource->childDraft,
            'isDraft' => isset($resource->draft_parent_id),
            'isExisting' => isset($resource->id),
        ]);
    }
}
