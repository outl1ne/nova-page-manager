<?php

namespace Outl1ne\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use Outl1ne\NovaPageManager\NPM;

class RegionField extends Field
{
    public $component = 'region-field';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, 'template', $resolveCallback);
    }

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $regions = $this->getAvailableRegions($resource);

        $this->withMeta([
            'asHtml' => true,
            'regions' => $regions,
            'existingRegions' => NPM::getRegionModel()::all()->pluck('template', 'id'),
        ]);

        $regionsTableName = NPM::getRegionsTableName();
        $locale = request()->get('locale');
        $this->creationRules('required', "unique:$regionsTableName,template,NULL,id,locale,$locale");
        $this->updateRules('required', "unique:$regionsTableName,template,{{resourceId}},id,locale,$locale");
    }

    public function getAvailableRegions($region = null): array
    {
        if (isset($region) && isset($region->id) && isset($region->template)) {
            return [$region->template];
        }

        return collect(NPM::getRegionTemplates())
            ->filter(function ($template) {
                return !NPM::getRegionModel()::where('template', $template::$name)->exists();
            })
            ->map(fn ($template) => $template::$name)
            ->toArray();
    }
}
