<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Illuminate\Support\Str;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Field;
use Illuminate\Http\UploadedFile;
use Outl1ne\PageManager\Template;
use Illuminate\Support\Facades\Log;
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
    protected $fieldOriginalAttributes = [];

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
        $this->fillFields($request, 'data', $fields, $model);

        if ($this->meta['type'] === Template::TYPE_PAGE) {
            $seoFields = new FieldCollection($this->seoFields);
            $this->fillFields($request, 'seo', $seoFields, $model);
        }
    }

    protected function fillFields($request, $attributeKey, $baseFields, $model)
    {
        $locales = array_keys(NPM::getLocales());
        $data = $request->get($attributeKey, []);

        foreach ($locales as $locale) {
            $dataAttributes = [];
            $fileAttributes = [];
            $localeData = $data[$locale] ?? [];

            foreach ($localeData as $k => $v) {
                $fullKey = $attributeKey . '->' . $locale . '->' . $k;

                if ($v instanceof UploadedFile) {
                    $fileAttributes[$fullKey] = $v;
                } else {
                    $dataAttributes[$fullKey] = $v;
                }
            }

            $fakeRequest = new NovaRequest([], $dataAttributes, [], [], $fileAttributes);
            $fakeRequest->headers = new HeaderBag(['Content-Type' => 'multipart/form-data']);
            $fakeRequest->setMethod(NovaRequest::METHOD_POST);

            $fields = $baseFields->map(fn ($field) => $this->transformFieldAttributes($field, "{$attributeKey}->{$locale}"));
            $fields->resolve((object) $localeData);
            $fields->map->fill($fakeRequest, $model);
        }
    }

    protected function transformFieldAttributes($baseField, $prefix)
    {
        $field = clone $baseField;
        $attribute = $field->attribute;

        if ($field->panel) {
            $sanitizedPanelName = Str::slug($field->panel, '_');
            $attribute = $sanitizedPanelName . '->' . $attribute;
        }

        $field->attribute = $prefix . '->' . $attribute;

        return $field;
    }
}
