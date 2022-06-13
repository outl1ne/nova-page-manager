<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\PageManager\Template;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

class PageManagerField extends Field
{
    public $component = 'page-manager-field';

    protected $template = null;
    protected $seoFields = null;

    public function __construct($type)
    {
        return $this->withMeta([
            'type' => $type,
            'locales' => NPM::getLocales(),
            'view' => app()->make(NovaRequest::class)->isResourceDetailRequest() ? 'detail' : 'form',
        ]);
    }

    public function withTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function withSeoFields($seoFields)
    {
        $this->seoFields = $seoFields;
        return $this;
    }

    public function fill(NovaRequest $request, $model)
    {
        $fields = new FieldCollection($this->template->fields($request));
        $model->data = $this->fillFieldsAndGetData($request, 'data', $fields);

        if ($this->meta['type'] === Template::TYPE_PAGE) {
            $seoFields = new FieldCollection($this->seoFields);
            $model->seo = $this->fillFieldsAndGetData($request, 'seo', $seoFields);
        }
    }

    protected function fillFieldsAndGetData($request, $attributeKey, $fields)
    {
        $locales = NPM::getLocales();

        $data = $request->get($attributeKey, '');
        $data = json_decode($data, true);

        $newData = [];
        foreach ($locales as $key => $localeName) {
            $fakeRequest = new NovaRequest();
            $fakeRequest->headers = new HeaderBag(['Content-Type' => 'application/json']);
            $fakeRequest->setMethod(NovaRequest::METHOD_POST);
            $fakeRequest->setJson(new ParameterBag($data ? $data[$key] : []));

            $fakeModel = (object) [];
            $fields->resolve((object) $fakeRequest->all());
            $fields->map->fill($fakeRequest, $fakeModel);
            $newData[$key] = (array) $fakeModel;
        }

        return $newData;
    }
}
