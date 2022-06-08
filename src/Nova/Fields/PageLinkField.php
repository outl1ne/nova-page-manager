<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Http\Requests\NovaRequest;

class PageLinkField extends Slug
{
    public $component = 'page-link-field';

    public function baseUrl($baseUrl)
    {
        return $this->withMeta([
            'baseUrl' => $baseUrl,
        ]);
    }

    public function jsonSerialize(): array
    {
        $novaRequest = app(NovaRequest::class);

        return array_merge([
            'view' => $novaRequest->isResourceDetailRequest() ? 'detail' : 'index',
        ], parent::jsonSerialize());
    }
}
