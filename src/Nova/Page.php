<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;
use OptimistDigital\NovaPageManager\NovaPageManager;
use OptimistDigital\NovaPageManager\Nova\Fields\ParentField;
use OptimistDigital\NovaPageManager\Nova\Fields\TemplateField;
use OptimistDigital\NovaPageManager\Nova\Fields\PublishedField;
use OptimistDigital\NovaPageManager\Nova\Fields\DraftButton;
use OptimistDigital\NovaLocaleField\LocaleField;

class Page extends TemplateResource
{
    public static $title = 'name';
    public static $model = 'OptimistDigital\NovaPageManager\Models\Page';
    public static $displayInNavigation = false;

    protected $type = 'page';

    public function fields(Request $request)
    {
        // Get base data
        $tableName = NovaPageManager::getPagesTableName();
        $templateClass = $this->getTemplateClass();
        $templateFieldsAndPanels = $this->getTemplateFieldsAndPanels();

        $fields = [
            ID::make()->sortable(),
            Text::make('Name', 'name')->rules('required'),
            Text::make('Slug', 'slug')
                ->creationRules('required', "unique:{$tableName},slug,NULL,id,locale,$request->locale")
                ->updateRules('required', "unique:{$tableName},slug,{{resourceId}},id,published,{{published}},locale,$request->locale")
                ->onlyOnForms(),
            Text::make('Slug', function () {
                $previewToken = $this->childDraft ? $this->childDraft->preview_token : $this->preview_token;
                $previewPart = $previewToken ? '?preview=' . $previewToken : '';
                $getPagePreviewUrlFn = NovaPageManager::getPagePreviewUrlFn();
                $path = $this->resource->path;
                $frontendLink = isset($getPagePreviewUrlFn) ? call_user_func($getPagePreviewUrlFn, $this->resource) . $previewPart : null;


                if (isset($frontendLink)) {
                    return <<<HTML
                        <div class='whitespace-no-wrap'>
                            <span class='bg-40 text-sm py-1 px-2 rounded-lg'>$path</span>
                            <a target='_blank' href='$frontendLink' class='text-sm py-1 px-2 text-primary no-underline dim font-bold'>View</a>
                        </div>
                    HTML;
                }
                return "<span class='bg-40 text-sm py-1 px-2 rounded-lg whitespace-no-wrap'>$path</span>";
            })->asHtml()->exceptOnForms(),
            ParentField::make('Parent', 'parent_id')->hideFromIndex(),
            TemplateField::make('Template', 'template'),
            LocaleField::make('Locale', 'locale', 'locale_parent_id')
                ->locales(NovaPageManager::getLocales())
                ->maxLocalesOnIndex(config('nova-page-manager.max_locales_shown_on_index', 4)),
        ];

        if (NovaPageManager::draftEnabled()) {
            $isDraft = (isset($this->draft_parent_id) || (!isset($this->draft_parent_id) && !$this->published && isset($this->id)));

            if (!(!$isDraft && ($request instanceof ResourceDetailRequest)) || isset($this->childDraft)) {
                $fields[] = DraftButton::make('Draft');
            }

            $fields[] = PublishedField::make('State', 'published');
        }

        if (isset($templateClass) && $templateClass::$seo) $fields[] = new Panel('SEO', $this->getSeoFields());

        if (count($templateFieldsAndPanels['fields']) > 0) {
            $fields[] = new Panel(
                'Page data',
                array_merge(
                    [Heading::make('Page data')->hideFromDetail()],
                    $templateFieldsAndPanels['fields']
                )
            );
        }

        if (count($templateFieldsAndPanels['panels']) > 0) {
            $fields = array_merge($fields, $templateFieldsAndPanels['panels']);
        }

        return $fields;
    }

    protected function getSeoFields()
    {
        return [
            Heading::make('SEO')->hideFromIndex()->hideWhenCreating()->hideFromDetail(),
            Text::make('SEO Title', 'seo_title')->hideFromIndex()->hideWhenCreating(),
            Text::make('SEO Description', 'seo_description')->hideFromIndex()->hideWhenCreating(),
            Image::make('SEO Image', 'seo_image')->hideFromIndex()->hideWhenCreating()
        ];
    }

    public function title()
    {
        return $this->name . ' (' . $this->slug . ')';
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->whereNull('draft_parent_id');
    }
}
