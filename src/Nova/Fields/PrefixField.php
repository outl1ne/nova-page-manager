<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;

class PrefixField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'prefix-field';

    public function parentSlug($path)
    {
        $pathParent = explode('/', $path);
        array_pop($pathParent);
        $pathParent = implode('/', $pathParent);
        $pathParent = empty($pathParent) ? '' : $pathParent . '/';

        return $this->withMeta([
            'path' => $pathParent,
        ]);
    }
}
