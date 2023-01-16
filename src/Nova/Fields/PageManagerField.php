<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Field;
use Outl1ne\PageManager\Template;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            $seoFields = FieldCollection::make(array_values($this->seoFields));
            $this->fillFields($request, 'seo', $seoFields, $model);
        }
    }

    public function getRules(NovaRequest $request): array
    {
        $rules = $this->getPageFields($request)
             ->applyDependsOn($request)
             ->withoutReadonly($request)
             ->mapWithKeys(fn ($field) => $field->getUpdateRules($request))
             ->all();

        return is_callable($rules) ? call_user_func($rules, $request) : $rules;
    }

    /**
     * @return void
     */
    protected function fillFields($request, $attributeKey, $baseFields, $model)
    {
        $flexibleAttrRegKey = $this->getFlexibleAttributeRegisterKey();

        $locales = ['__', ...array_keys(NPM::getLocales())];
        $body = $request->get($attributeKey, []);
        $files = $request->files->get($attributeKey, []);
        $data = array_merge_recursive($body, $files);

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
                } elseif ($flexibleKeys) {
                    if ($k === $flexibleAttrRegKey) {
                        // Modify flexible registration keys
                        $dataAttributes[$fullKey] = array_map(fn ($fKey) => "{$attributeKey}->{$locale}->{$fKey}", $flexibleKeys);
                    } elseif (in_array($k, $flexibleKeys)) {
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

    public function getPageFields(NovaRequest $request): FieldCollection
    {
        $locales = collect(array_keys(NPM::getLocales()));
        $baseFields = new FieldCollection($this->filter($this->template->fields($request)));
        $fields = new FieldCollection();

        foreach ($locales as $locale) {
            foreach ($baseFields as $field) {
                $field = $this->transformFieldAttributes(clone $field, "data.{$locale}", seperator: '.');
                $fields->push($field);
            }
        }

        return $fields;
    }

    public static function transformFieldAttributes($field, $prefix = null, $seperator = '->'): Field
    {
        if (empty($field->meta['originalAttribute'])) {
            $field->withMeta(['originalAttribute' => $field->attribute]);
        }

        $attribute = $field->meta['originalAttribute'];

        if (isset($field->assignedPanel->meta['fieldPrefix'])) {
            $fieldPrefix = $field->assignedPanel->meta['fieldPrefix'];
            $attribute = $fieldPrefix . $seperator . $attribute;
        }

        if ($prefix) {
            $attribute = $prefix . $seperator . $attribute;
        }
        $field->attribute = $attribute;

        return $field;
    }

    /**
     * @return <missing>|array
     */
    protected function getFlexibleCompatibleValue($value)
    {
        if (!class_exists('\Whitecube\NovaFlexibleContent\Http\FlexibleAttribute')) {
            return $value;
        }
        if (empty($value) || !is_string($value)) {
            return $value;
        }
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

    /**
     * @return <missing>|null
     */
    protected function getFlexibleAttributeRegisterKey()
    {
        return class_exists('\Whitecube\NovaFlexibleContent\Http\FlexibleAttribute')
            ? \Whitecube\NovaFlexibleContent\Http\FlexibleAttribute::REGISTER
            : null;
    }
}
