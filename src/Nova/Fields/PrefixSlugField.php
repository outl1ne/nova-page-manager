<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Http\Requests\NovaRequest;

class PrefixSlugField extends Slug
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'prefix-slug-field';

    public function pathPrefix($path = [])
    {
        return $this->withMeta([
            'pathPrefix' => $path,
        ]);
    }

    public function pathSuffix($path)
    {
        return $this->withMeta([
            'pathSuffix' => '/' . $path,
        ]);
    }

    public function fill(NovaRequest $request, $model)
    {
        $attribute = $this->meta['translatable']['original_attribute'] ?? $this->attribute;

        $data = $request->get($attribute);
        $locales = NPM::getLocales();

        $newSlugs = [];
        foreach ($locales as $key => $localeName) {
            $slug = $data[$key] ?? '';
            $slug = trim($slug);

            // Remove all slashes
            $slug = preg_replace('/[\/]+/', '', $slug);

            $newSlugs[$key] = $slug;
        }

        $model->{$attribute} = $newSlugs;
    }

    public function jsonSerialize(): array
    {
        $novaRequest = app(NovaRequest::class);

        $showCustomizeButton = false;

        if ($novaRequest->isUpdateOrUpdateAttachedRequest()) {
            $this->readonly();
            $showCustomizeButton = true;
        }

        return array_merge([
            'updating' => $novaRequest->isUpdateOrUpdateAttachedRequest(),
            'separator' => '-',
            'showCustomizeButton' => $showCustomizeButton,
        ], parent::jsonSerialize());
    }
}
