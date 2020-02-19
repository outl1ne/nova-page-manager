<?php

namespace OptimistDigital\NovaPageManager\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use OptimistDigital\NovaLocaleField\LocaleField;
use OptimistDigital\NovaPageManager\Nova\Fields\ParentField;
use OptimistDigital\NovaPageManager\Nova\Fields\PrefixField;
use OptimistDigital\NovaPageManager\Nova\Fields\TemplateField;
use OptimistDigital\NovaPageManager\NovaPageManager;

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
        $locales = NovaPageManager::getLocales();
        $hasManyDifferentLocales = Page::select('locale')->distinct()->get()->count() > 1;

        $fields = [
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
                ->creationRules('required', "unique:{$tableName},slug,NULL,id,locale,$request->locale,parent_id," . ($this->resource->parent_id ?? 'NULL'), 'alpha_dash_or_slash')
                ->updateRules('required', "unique:{$tableName},slug,{{resourceId}},id,published,{{published}},locale,$request->locale,parent_id," . ($this->resource->parent_id ?? 'NULL'), 'alpha_dash_or_slash')
                ->onlyOnForms()
                ->parentSlug($this->resource->path),
            Text::make('Slug', function () {
                $previewToken = $this->childDraft ? $this->childDraft->preview_token : $this->preview_token;
                $previewPart = $previewToken ? '?preview=' . $previewToken : '';
                $pagePath = $this->resource->path;
                $pageBaseUrl = NovaPageManager::getPageUrl($this->resource);
                $pageUrl = !empty($pageBaseUrl) ? $pageBaseUrl . $previewPart : null;
                $buttonText = $this->resource->isDraft() ? 'View draft' : 'View';

                if (empty($pageBaseUrl)) return "<span class='bg-40 text-sm py-1 px-2 rounded-lg whitespace-no-wrap'>$pagePath</span>";

                return "<div class='whitespace-no-wrap'>
                            <span class='bg-40 text-sm py-1 px-2 rounded-lg'>$pagePath</span>
                            <a target='_blank' href='$pageUrl' class='text-sm py-1 px-2 text-primary no-underline dim font-bold'>$buttonText</a>
                        </div>";
            })->asHtml()->exceptOnForms(),

            ParentField::make('Parent', 'parent_id')->hideFromIndex(),
            TemplateField::make('Template', 'template')->sortable(),
        ];


        if (NovaPageManager::hasNovaLang()) {
            $fields[] = \OptimistDigital\NovaLang\NovaLangField\NovaLangField::make('Locale', 'locale', 'locale_parent_id')->onlyOnForms();
        } else {
            $fields[] = LocaleField::make('Locale', 'locale', 'locale_parent_id')
                ->locales($locales)
                ->onlyOnForms();
        }

        if (count($locales) > 1) {
            $fields[] = LocaleField::make('Locale', 'locale', 'locale_parent_id')
                ->locales($locales)
                ->exceptOnForms()
                ->maxLocalesOnIndex(config('nova-page-manager.max_locales_shown_on_index', 4));
        } else if ($hasManyDifferentLocales) {
            $fields[] = Text::make('Locale', 'locale')->exceptOnForms();
        }

        if (NovaPageManager::hasNovaDrafts()) {
            $fields[] = \OptimistDigital\NovaDrafts\PublishedField::make('State', 'published');
            $fields[] = \OptimistDigital\NovaDrafts\DraftButton::make('Draft');
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
        $table = NovaPageManager::getPagesTableName();
        $localeColumn = $table . '.locale';

        $query->selectRaw("{$table}.*, CONCAT(COALESCE(p3.name, ''), COALESCE(p2.name, ''), COALESCE(p1.name, ''), COALESCE({$table}.name, '')) AS hierarchy_order")
            ->doesntHave('childDraft')
            ->leftJoin("{$table} AS p1", 'p1.id', '=', "{$table}.parent_id")
            ->leftJoin("{$table} AS p2", 'p2.id', '=', 'p1.parent_id')
            ->leftJoin("{$table} AS p3", 'p3.id', '=', 'p2.parent_id')
            ->orderByRaw('hierarchy_order');

        if (NovaPageManager::hasNovaLang()) {
            $query->where(function ($subQuery) use ($localeColumn) {
                $subQuery->where($localeColumn, nova_lang_get_active_locale())
                    ->orWhereNotIn($localeColumn, array_keys(nova_lang_get_all_locales()));
            });
        }

        return $query;
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
