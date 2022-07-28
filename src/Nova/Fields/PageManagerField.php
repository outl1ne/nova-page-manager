<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Field;
use Illuminate\Http\UploadedFile;
use Outl1ne\PageManager\Template;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpFoundation\HeaderBag;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class PageManagerField extends Field
{
    use ConditionallyLoadsAttributes;

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
        $fields = new FieldCollection($this->filter($this->template->fields($request)));

        $model->data = $this->fillFieldsAndGetData($request, 'data', $fields, $model->data);

        if ($this->meta['type'] === Template::TYPE_PAGE) {
            $seoFields = new FieldCollection($this->seoFields);

            $model->seo = $this->fillFieldsAndGetData($request, 'seo', $seoFields, $model->seo);
        }
    }

    protected function fillFieldsAndGetData($request, $attributeKey, $fields, $existingData)
    {
        $locales = NPM::getLocales();

        $all = $request->all();
        $data = $all[$attributeKey] ?? [];

        $newData = [];
        foreach ($locales as $key => $localeName) {
            $dataAttributes = [];
            $fileAttributes = [];

            $localeData = $data[$key] ?? [];
            foreach ($localeData as $k => $v) {
                if ($v instanceof UploadedFile) {
                    $fileAttributes[$k] = $v;
                } else {
                    $dataAttributes[$k] = $v;
                }
            }

            $fakeRequest = new NovaRequest([], $dataAttributes, [], [], $fileAttributes);
            $fakeRequest->headers = new HeaderBag(['Content-Type' => 'multipart/form-data']);
            $fakeRequest->setMethod(NovaRequest::METHOD_POST);

            $fakeModel = (object) [];
            $fields->resolve((object) $localeData);
            $fields->map->fill($fakeRequest, $fakeModel);

            $newData[$key] = array_merge(
                $existingData[$key] ?? [],
                (array) $fakeModel,
            );
        }

        return $newData;
    }
}
