<?php

namespace Outl1ne\PageManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\ResolvesFields;
use Outl1ne\PageManager\Template;
use Illuminate\Routing\Controller;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\PageManager\Nova\Fields\PageManagerField;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Support\Facades\Storage;

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
        $templateType = NPM::getTemplateClassType($templateClass::class);

        $fieldsData = [];
        $seoFieldsData = [];

        $localeKeys = ['__', ...array_keys($locales)];
        foreach ($localeKeys as $key) {
            $dataObject = (object) ($model->data[$key] ?? []);
            $fields = $templateClass->fields($request);

            // Fix DateTime fields
            collect($fields)->each(function ($field) use ($dataObject) {
                if ($field instanceof \Laravel\Nova\Fields\DateTime) {
                    $currentValue = $dataObject->{$field->attribute} ?? null;
                    if ($currentValue) {
                        $dataObject->{$field->attribute} = Carbon::parse($currentValue);
                    }
                }
            });

            $fieldCollection = FieldCollection::make($this->filter($fields));

            if ($isSyncRequest) {
                $fieldCollection = $fieldCollection
                    ->resolve($dataObject)
                    ->filter(function ($field) use ($request) {
                        $isSameAttribute = $request->query('field') === $field->attribute;
                        $isSameComponent = in_array($request->query('component'), [$field->dependentComponentKey(), $field->component]);
                        return $isSameAttribute && $isSameComponent;
                    })->each->syncDependsOn(resolve(NovaRequest::class));

                return response()->json($fieldCollection->first(), 200);
            }

            $fieldCollection->each(fn ($field) => $field->template = $templateClass);
            $fieldCollection = $fieldCollection->map(fn ($field) => PageManagerField::transformFieldAttributes($field));
            $fieldCollection->resolve($dataObject);
            $fieldCollection->assignDefaultPanel(__('novaPageManager.defaultPanelName'));
            $fieldsData[$key] = $fieldCollection;

            if ($templateType === Template::TYPE_PAGE) {
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
        foreach ($localeKeys as $key) {
            $panelsData[$key] = $this->resolvePanelsFromFields(
                app()->make(NovaRequest::class),
                $fieldsData[$key],
                __('novaPageManager.defaultPanelName'),
            );

            if ($templateType === Template::TYPE_PAGE) {
                 $fieldCollection = FieldCollection::make($seoFieldsData);

                $seoPanelsData[$key] = $this->resolvePanelsFromFields(
                    app()->make(NovaRequest::class),
                    $fieldCollection,
                    __('novaPageManager.seoPanelName')
                );
            }
        }

        // Re-map everything into format
        // Panels: [{ fields: { et: [], en: [] } }, { fields: [], npmDoNotTranslate: true }]
        $formattedPanels = $panelsData['__'] ?? [];
        foreach ($formattedPanels as $i => &$panel) {
            if ($panel->meta['npmDoNotTranslate'] ?? false) {
                $panel->data = $panelsData['__'][$i]->data;
                $panel->meta['fields'] = $panelsData['__'][$i]->meta['fields'];
            } else {
                $panel->data = [];
                $panel->meta['fields'] = [];

                foreach (array_keys($locales) as $locale) {
                    $panel->data[$locale] = $panelsData[$locale][$i]->data;
                    $panel->meta['fields'][$locale] = $panelsData[$locale][$i]->meta['fields'];
                }
            }
        }

        $formattedSeoPanels = $seoPanelsData['__'] ?? [];
        foreach ($formattedSeoPanels as $i => &$panel) {
            $panel->data = [];
            $panel->meta['fields'] = [];

            foreach (array_keys($locales) as $locale) {
                $panel->data[$locale] = $seoPanelsData[$locale][$i]->data;
                $panel->meta['fields'][$locale] = $seoPanelsData[$locale][$i]->meta['fields'];
            }
        }

        return [
            'panelsWithFields' => $formattedPanels,
            'seoPanelsWithFields' => $formattedSeoPanels,
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
        $fileName = $data[$locale][$fieldAttribute];
        $data[$locale][$fieldAttribute] = null;
        $model->{$panelType} = $data;
        $model->timestamps = false;
        $model->save();
        Storage::disk('public')->delete($fileName);

        return response('', 204);
    }

    public function downloadFile(NovaRequest $request)
    {
        $panelType = $request->route('panelType');
        $resourceType = $request->route('resourceType');
        $locale = $request->route('locale');
        $resourceId = $request->route('resourceId');
        $fieldAttribute = $request->route('fieldAttribute');

        if (!in_array($panelType, ['seo', 'data'])) return response()->json(['error' => 'Invalid panel type.'], 400);
        if (!in_array($resourceType, ['pages', 'regions'])) return response()->json(['error' => 'Invalid resource type.'], 400);

        $modelClass = $resourceType === 'pages' ? NPM::getPageModel() : NPM::getRegionModel();
        $resourceClass = $resourceType === 'pages' ? NPM::getPageResource() : NPM::getRegionResource();
        $templates = $resourceType === 'pages' ? NPM::getPageTemplates() : NPM::getRegionTemplates();

        $model = $modelClass::findOrFail($resourceId);
        $resource = new $resourceClass($model);
        $template = new $templates[$model->template]['class'];
        $resource->authorizeToView($request);

        $fields = $panelType === 'seo' ? NPM::getSeoFields() : $template->fields($request);
        $fields = FieldCollection::make(array_values($this->filter($fields)));
        $field = $fields->findFieldByAttribute($fieldAttribute, fn () => abort(404));
        $field = PageManagerField::transformFieldAttributes($field, "{$panelType}->{$locale}");
        $field->resolveForDisplay($resource->resource);

        return $field->toDownloadResponse($request, $resource);
    }
}
