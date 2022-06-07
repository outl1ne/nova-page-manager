<?php

namespace Outl1ne\PageManager\Http\Controllers;

use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Outl1ne\PageManager\Template;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\ResolvesFields;

class PageManagerController extends Controller
{
    use ResolvesFields;

    public function getFields(Request $request, $type, $resourceId)
    {
        $locales = NPM::getLocales();

        $templates = $type === Template::TYPE_PAGE
            ? NPM::getPageTemplates()
            : NPM::getRegionTemplates();

        $modelClass = $type === Template::TYPE_PAGE
            ? NPM::getPageModel()
            : NPM::getRegionModel();

        $model = $modelClass::find($resourceId);
        if (!$model) return response('Model not found', 404);

        $template = $templates[$model->template] ?? null;
        if (!$template) return response('Template not found.', 404);

        // Resolve with values
        $templateClass = new $template['class'];

        $fieldsData = [];
        foreach ($locales as $key => $locale) {
            $dataObject = (object) ($model->data[$key] ?? []);

            $fields = $templateClass->fields($request);
            $fieldCollection = FieldCollection::make($fields);
            $fieldCollection->resolve($dataObject);
            $fieldCollection->assignDefaultPanel(__('novaPageManager.defaultPanelName'));
            $fieldsData[$key] = $fieldCollection;
        }

        $panelsData = [];
        foreach ($locales as $key => $locale) {
            $panelsData[$key] = $this->resolvePanelsFromFields(
                app()->make(NovaRequest::class),
                $fieldsData[$key],
                __('novaPageManager.defaultPanelName'),
            );
        }

        return [
            'panelsWithFields' => $panelsData,
        ];
    }
}
