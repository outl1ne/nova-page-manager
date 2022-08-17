<?php

namespace Outl1ne\PageManager\Http\Controllers;

use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\ResolvesFields;
use Outl1ne\PageManager\Template;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\PageManager\Nova\Fields\PageManagerField;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class PageManagerController extends Controller
{
    use ResolvesFields, ConditionallyLoadsAttributes;

    public function getFields(Request $request, $type, $resourceId, $isSyncRequest = false)
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
            $fieldCollection = FieldCollection::make($this->filter($fields));

            if ($isSyncRequest) {
                $fieldCollection = $fieldCollection->filter(function ($field) use ($request) {
                    return $request->query('field') === $field->attribute && $request->query('component') === $field->component;
                })->each->syncDependsOn(resolve(NovaRequest::class));

                return response()->json($fieldCollection->first(), 200);
            }

            $fieldCollection->each(fn ($field) => $field->template = $templateClass);
            $fieldCollection = $fieldCollection->map(fn ($field) => PageManagerField::transformFieldAttributes($field));
            $fieldCollection->resolve($dataObject);
            $fieldCollection->assignDefaultPanel(__('novaPageManager.defaultPanelName'));
            $fieldsData[$key] = $fieldCollection;

            if ($templateClass === Template::TYPE_PAGE) {
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
        }

        $panelsData = [];
        $seoPanelsData = [];
        foreach ($locales as $key => $locale) {
            $panelsData[$key] = $this->resolvePanelsFromFields(
                app()->make(NovaRequest::class),
                $fieldsData[$key],
                __('novaPageManager.defaultPanelName'),
            );

            if ($template === Template::TYPE_PAGE) {
                $seoPanelsData[$key] = $this->resolvePanelsFromFields(
                    app()->make(NovaRequest::class),
                    $seoFieldsData[$key],
                    __('novaPageManager.seoPanelName'),
                );
            }
        }

        return [
            'panelsWithFields' => $panelsData,
            'seoPanelsWithFields' => $seoPanelsData,
        ];
    }

    public function syncUpdateFields(Request $request, $panelType, $resourceType, $locale, $resourceId)
    {
        $resourceType = $resourceType === 'pages' ? Template::TYPE_PAGE : Template::TYPE_REGION;
        return $this->getFields($request, $resourceType, $resourceId, true);
    }

    public function deleteFile(Request $request)
    {
        $panelType = $request->route('panelType');
        $resourceType = $request->route('resourceType');
        $locale = $request->route('locale');
        $resourceId = $request->route('resourceId');
        $fieldAttribute = $request->route('fieldAttribute');

        if (!in_array($panelType, ['seo', 'data'])) return response()->json(['error' => 'Invalid panel type.'], 400);
        if (!in_array($resourceType, ['pages', 'regions'])) return response()->json(['error' => 'Invalid resource type.'], 400);

        $modelClass = $resourceType === 'pages'
            ? NPM::getPageModel()
            : NPM::getRegionModel();

        $model = $modelClass::findOrFail($resourceId);
        $data = $model->{$panelType};
        $data[$locale][$fieldAttribute] = null;
        $model->{$panelType} = $data;
        $model->save();

        return response('', 204);
    }
}
