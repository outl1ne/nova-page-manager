<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

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

    public function jsonSerialize()
    {
        $request = app(NovaRequest::class);

        $showCustomizeButton = false;

        if ($request->isUpdateOrUpdateAttachedRequest()) {
            $this->readonly();
            $showCustomizeButton = true;
        }

        return array_merge([
            'updating' => $request->isUpdateOrUpdateAttachedRequest(),
            'from' => 'name',
            'separator' => '-',
            'showCustomizeButton' => $showCustomizeButton,
        ], parent::jsonSerialize());
    }
}
