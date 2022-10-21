<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\ResolvesFields;
use Outl1ne\PageManager\Template;
use Laravel\Nova\PerformsValidation;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class PageManagerField extends Field
{
    use ConditionallyLoadsAttributes, PerformsValidation, ResolvesFields;

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

    protected function fields()
    {
        return $this->template->fields(request());
    }

    public function fill(NovaRequest $request, $model)
    {
        $fields = new FieldCollection($this->filter($this->template->fields($request)));
        $this->fillFields($request, 'data', $fields, $model);

        if ($this->meta['type'] === Template::TYPE_PAGE) {
            $seoFields = FieldCollection::make(array_values($this->seoFields));
            $this->fillFields($request, 'seo', $seoFields, $model);
        }

        // Validate
        // $validator = Validator::make($request->all(), static::rulesForUpdate($request, $this));
        // if ($validator->fails()) {
        //     $errors = $validator->errors();
        //     $messages = collect($errors->getMessages())->mapWithKeys(function ($value, $key) {
        //         return ["data.{$key}" => $value];
        //     })->toArray();

        //     return abort(
        //         response()->json(
        //             ['errors' => $messages, 'message' => $validator->messages()->first()],
        //             422
        //         )
        //     );
        // }
    }

    protected function fillFields($request, $attributeKey, $baseFields, $model)
    {
        $flexibleAttrRegKey = $this->getFlexibleAttributeRegisterKey();

        $locales = ['__', ...array_keys(NPM::getLocales())];
        $body = $request->get($attributeKey, []);
        $files = $request->files->get($attributeKey, []);
        $data = array_merge($body, $files);

        foreach ($locales as $locale) {
            $dataAttributes = [];
            $fileAttributes = [];
            $localeData = $data[$locale] ?? [];

            $flexibleKeys = null;
            if ($flexibleAttrRegKey && isset($localeData[$flexibleAttrRegKey])) {
                $flexibleKeys = json_decode($localeData[$flexibleAttrRegKey], true);
                $localeData[$flexibleAttrRegKey] = $flexibleKeys;
            }

            foreach ($localeData as $k => $v) {
                $fullKey = "{$attributeKey}->{$locale}->{$k}";

                if ($v instanceof UploadedFile) {
                    $fileAttributes[$fullKey] = $v;
                } else if ($flexibleKeys) {
                    if ($k === $flexibleAttrRegKey) {
                        // Modify flexible registration keys
                        $dataAttributes[$fullKey] = array_map(fn ($fKey) => "{$attributeKey}->{$locale}->{$fKey}", $flexibleKeys);
                    } else if (in_array($k, $flexibleKeys)) {
                        // Decode flexible values
                        $dataAttributes[$fullKey] = $this->getFlexibleCompatibleValue($v);
                    } else {
                        $dataAttributes[$fullKey] = $v;
                    }
                } else {
                    $dataAttributes[$fullKey] = $v;
                }
            }

            $fakeRequest = new NovaRequest([], $dataAttributes, [], [], $fileAttributes);
            $fakeRequest->headers = new HeaderBag(['Content-Type' => 'multipart/form-data']);
            $fakeRequest->setMethod(NovaRequest::METHOD_POST);

            $fields = $baseFields->map(fn ($field) => $this->transformFieldAttributes($field, "{$attributeKey}->{$locale}"));
            $fields->resolve((object) array_merge($dataAttributes, $fileAttributes));
            $fields->map->fill($fakeRequest, $model);
        }
    }

    public static function transformFieldAttributes($field, $prefix = null)
    {
        if (empty($field->meta['originalAttribute'])) {
            $field->withMeta(['originalAttribute' => $field->attribute]);
        }

        $attribute = $field->meta['originalAttribute'];

        if (isset($field->assignedPanel->meta['fieldPrefix'])) {
            $fieldPrefix = $field->assignedPanel->meta['fieldPrefix'];
            $attribute = $fieldPrefix . '->' . $attribute;
        }

        if ($prefix) $attribute = $prefix . '->' . $attribute;
        $field->attribute = $attribute;

        return $field;
    }

    protected function getFlexibleCompatibleValue($value)
    {
        if (!class_exists('\Whitecube\NovaFlexibleContent\Http\FlexibleAttribute')) return $value;
        if (empty($value) || !is_string($value)) return $value;
        $value = json_decode($value, true);

        return array_map(function ($group) {
            $clean = [
                'layout' => $group['layout'] ?? null,
                'key' => $group['key'] ?? null,
                'attributes' => [],
            ];

            foreach ($group['attributes'] ?? [] as $attribute => $value) {
                $newAttribute = new \Whitecube\NovaFlexibleContent\Http\FlexibleAttribute($attribute, $clean['key']);
                $newAttribute->setDataIn($clean['attributes'], $value);
            }

            $flexAttrRegKey = $this->getFlexibleAttributeRegisterKey();
            if ($subFlexbiles = $clean['attributes'][$flexAttrRegKey] ?? null) {
                $subFlexbiles = json_decode($subFlexbiles, true);
                $clean['attributes'][$flexAttrRegKey] = $subFlexbiles;
                $subFlexbiles = array_map(function ($sfAttr) use ($clean) {
                    return (new \Whitecube\NovaFlexibleContent\Http\FlexibleAttribute($sfAttr, $clean['key']))->name;
                }, $subFlexbiles);

                foreach ($clean['attributes'] as $attribute => $value) {
                    $tempAttribute = new \Whitecube\NovaFlexibleContent\Http\FlexibleAttribute($attribute, $clean['key']);
                    if (in_array($tempAttribute->name, $subFlexbiles)) {
                        $clean['attributes'][$attribute] = $this->getFlexibleCompatibleValue($value);
                    }
                }
            }

            return $clean;
        }, $value);
    }

    protected function getFlexibleAttributeRegisterKey()
    {
        return class_exists('\Whitecube\NovaFlexibleContent\Http\FlexibleAttribute')
            ? \Whitecube\NovaFlexibleContent\Http\FlexibleAttribute::REGISTER
            : null;
    }
}
