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
use OptimistDigital\NovaPageManager\Nova\Fields\PrefixField;
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
    public static $search = ['name', 'slug', 'template'];

    protected $type = 'page';

    public function fields(Request $request)
    {
        // Get base data
        $tableName = NovaPageManager::getPagesTableName();
        $templateClass = $this->getTemplateClass();
        $templateFieldsAndPanels = $this->getTemplateFieldsAndPanels();

        $fields = [
            ID::make()->sortable(),
            Text::make('Name', function () {
                $pagePath = $this->resource->path;
                $name = $this->resource->name;
                $parentPaths = (explode('/', $pagePath));
                array_shift($parentPaths);
                array_pop($parentPaths);
                return str_repeat('— ', count($parentPaths)) . $name;
            })->rules('required')->onlyOnIndex(),
            Text::make('Name', 'name')->rules('required')->hideFromIndex(),
            PrefixField::make('Slug', 'slug')
                ->creationRules('required', "unique:{$tableName},slug,NULL,id,locale,$request->locale")
                ->updateRules('required', "unique:{$tableName},slug,{{resourceId}},id,published,{{published}},locale,$request->locale")
                ->onlyOnForms()
                ->parentSlug($this->resource->path),
            Text::make('Slug', function () {
                $previewToken = $this->childDraft ? $this->childDraft->preview_token : $this->preview_token;
                $previewPart = $previewToken ? '?preview=' . $previewToken : '';
                $pagePath = $this->resource->path;
                $pageBaseUrl = NovaPageManager::getPageUrl($this->resource);
                $pageUrl = !empty($pageBaseUrl) ? $pageBaseUrl . $previewPart : null;
                $buttonText = $this->resource->isDraft() ? 'View draft' : 'View';


                if (empty($pageBaseUrl)) return <<<HTML
                    <span class="bg-40 text-sm py-1 px-2 rounded-lg whitespace-no-wrap">$pagePath</span>
                HTML;

                return <<<HTML
                    <div class="whitespace-no-wrap">
                        <span class="bg-40 text-sm py-1 px-2 rounded-lg">$pagePath</span>
                        <a target="_blank" href="$pageUrl" class="text-sm py-1 px-2 text-primary no-underline dim font-bold">$buttonText</a>
                    </div>
                HTML;
            })->asHtml()->exceptOnForms(),

            ParentField::make('Parent', 'parent_id')->hideFromIndex(),
            TemplateField::make('Template', 'template')->sortable(),
            LocaleField::make('Locale', 'locale', 'locale_parent_id')
                ->locales(NovaPageManager::getLocales())
                ->maxLocalesOnIndex(config('nova-page-manager.max_locales_shown_on_index', 4)),
        ];

        if (NovaPageManager::draftsEnabled()) {
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
        return $query->selectRaw("nova_page_manager_pages.*, CONCAT(COALESCE(CONCAT(CONCAT(COALESCE(CONCAT(grandparents.name, '/'), ''), parents.name), '/'), ''), nova_page_manager_pages.name) AS hierarchy_order")
            ->doesntHave('childDraft')
            ->leftJoin('nova_page_manager_pages AS parents', 'parents.id', '=', 'nova_page_manager_pages.parent_id')
            ->leftJoin('nova_page_manager_pages AS grandparents', 'grandparents.id', '=', 'parents.parent_id')
            ->orderByRaw('hierarchy_order');
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        if (empty($orderings)) {
            return $query;
        }

        return parent::applyOrderings($query, $orderings);
    }
}
