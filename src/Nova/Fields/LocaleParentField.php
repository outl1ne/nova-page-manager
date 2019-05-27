<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use OptimistDigital\NovaPageManager\NovaPageManager;
use OptimistDigital\NovaPageManager\Models\Page;
use OptimistDigital\NovaPageManager\Models\Region;

class LocaleParentField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'locale-parent-field';

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
        // Mask this field as Parent ID
        parent::__construct($name, 'locale_parent_id', $resolveCallback);

        $_pages = Page::whereNull('locale_parent_id')->get();
        $_regions = Region::whereNull('locale_parent_id')->get();

        $resources = $_pages->merge($_regions)
            ->flatten()
            ->map(function ($template) {
                $label = $template->name;
                if (!empty($template->slug)) $label .= ' (' . $template->slug . ')';

                return [
                    'label' => $label,
                    'id' => $template->id
                ];
            })
            ->pluck('label', 'id');

        $this->withMeta([
            'asHtml' => true,
            'resources' => $resources,
        ]);
    }

    /**
     * Resolve the field's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $value = [];
        $locales = NovaPageManager::getLocales();
        $model = get_class($resource);
        $id = $resource->id;
        $localeParentId = $resource->locale_parent_id;

        // ID
        $value['id'] = $id;
        $value['locale'] = $resource->locale;
        $value['locale_parent_id'] = $resource->locale_parent_id;

        // Is master
        $queryParentId = empty($localeParentId) ? $id : $localeParentId;
        $children = $model::where('locale_parent_id', $queryParentId)->where('id', '!=', $id)->get();
        $value['locales'] = [];
        foreach ($locales as $key => $localeName) {
            $value['locales'][$key] = $children->first(function ($c) use ($key) {
                return $c->locale === $key;
            });
        }

        $this->value = $value;
    }
}
