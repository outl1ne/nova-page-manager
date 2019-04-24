<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use OptimistDigital\NovaPageManager\NovaPageManager;
use OptimistDigital\NovaPageManager\Models\Region;

class RegionField extends Field
{
    public $component = 'region-field';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, 'template', $resolveCallback);

        $regions = array_map(function ($template) {
            return $template::$name;
        }, NovaPageManager::getRegionTemplates());

        $this->withMeta([
            'asHtml' => true,
            'regions' => $regions,
            'existingRegions' => Region::whereNull('locale_parent_id')->get()->pluck('template', 'id'),
        ]);

        $this->rules('required', 'in:' . implode(',', $regions));
    }
}
