<?php

namespace Outl1ne\PageManager\Nova\Fields;

use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

class PageManagerField extends Field
{
    public $component = 'page-manager-field';

    protected $template = null;

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

    public function fill(NovaRequest $request, $model)
    {
        $locales = NPM::getLocales();
        $fields = new FieldCollection($this->template->fields($request));
        $data = $request->get('data', '');
        $data = json_decode($data, true);

        $newData = [];
        foreach ($locales as $key => $localeName) {
            $fakeRequest = new NovaRequest();
            $fakeRequest->headers = new HeaderBag(['Content-Type' => 'application/json']);
            $fakeRequest->setMethod(NovaRequest::METHOD_POST);
            $fakeRequest->setJson(new ParameterBag($data[$key]));

            $fakeModel = (object) [];
            $fields->resolve((object) $fakeRequest->all());
            $fields->map->fill($fakeRequest, $fakeModel);
            $newData[$key] = (array) $fakeModel;
        }

        $model->data = $newData;
    }

    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        return $fields->map->fill($request, $model)->filter(function ($callback) {
            return is_callable($callback);
        })->values()->all();
    }
}
