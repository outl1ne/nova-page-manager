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
        $seoFieldsData = [];
        foreach ($locales as $key => $locale) {
            $dataObject = (object) ($model->data[$key] ?? []);
            $fields = $templateClass->fields($request);
            $fieldCollection = FieldCollection::make($fields);
            $fieldCollection->each(fn ($field) => $field->template = $templateClass);
            $fieldCollection->resolve($dataObject);
            $fieldCollection->assignDefaultPanel(__('novaPageManager.defaultPanelName'));
            $fieldsData[$key] = $fieldCollection;

            // SEO fields
            $seoFields = NPM::getSeoFields();
            if ($seoFields) {
                $dataObject = (object) ($model->seo[$key] ?? []);
                $seoFieldCollection = FieldCollection::make($seoFields);
                $seoFieldCollection->each(fn ($field) => $field->template = $templateClass);
                $seoFieldCollection->resolve($dataObject);
                $seoFieldCollection->assignDefaultPanel(__('novaPageManager.seoPanelName'));
                $seoFieldsData[$key] = $seoFieldCollection;
            }
        }

        $panelsData = [];
        $seoPanelsData = [];
        foreach ($locales as $key => $locale) {
            $panelsData[$key] = $this->resolvePanelsFromFields(
                app()->make(NovaRequest::class),
                $fieldsData[$key],
                __('novaPageManager.defaultPanelName'),
            );

            $seoPanelsData[$key] = $this->resolvePanelsFromFields(
                app()->make(NovaRequest::class),
                $seoFieldsData[$key],
                __('novaPageManager.seoPanelName'),
            );
        }

        return [
            'panelsWithFields' => $panelsData,
            'seoPanelsWithFields' => $seoPanelsData,
        ];
    }
}
