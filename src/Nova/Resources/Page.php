<?php

namespace Outl1ne\NovaPageManager\Nova\Resources;

use Laravel\Nova\Panel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Outl1ne\NovaPageManager\NPM;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class Page extends TemplateResource
{
    public static $title = 'name';
    public static $model = null;
    public static $displayInNavigation = false;
    public static $search = ['name', 'slug', 'template'];

    protected $type = 'page';

    public function __construct($resource)
    {
        self::$model = NPM::getPageModel();
        parent::__construct($resource);
    }

    public static function newModel()
    {
        $model = empty(self::$model) ? NPM::getPageModel() : self::$model;
        return new $model;
    }

    public function fields(Request $request)
    {
        // Get base data
        $templateClass = $this->getTemplateClass();
        $templateFieldsAndPanels = get_class($request) === ResourceIndexRequest::class ? [] : $this->getTemplateFieldsAndPanels();

        $fields = [
            // Name on index view
            Text::make(__od_npm('nameField'), function () {
                $pagePath = $this->resource->path;
                $name = $this->resource->name;
                $parentPaths = (explode('/', $pagePath));
                array_shift($parentPaths);
                array_pop($parentPaths);
                return str_repeat('— ', count($parentPaths)) . $name;
            })->onlyOnIndex(),

            // Name on detail/form views
            Text::make(__od_npm('nameField'), 'name')
                ->required()
                ->rules('required', 'max:255')
                ->hideFromIndex(),

            // Slug on form views
            Slug::make(__od_npm('slugField'), 'slug')
                ->translatable(NPM::getLocales())
                ->from('name')
                ->onlyOnForms(),

            // Slug on index/detail views
            Text::make(__od_npm('slugField'), function () {
                $pagePath = $this->resource->path;
                $pageUrl = NPM::getPageUrl($this->resource);
                $buttonText = __('novaPageManager.view');

                if (empty($pageUrl)) return "<span class='bg-40 text-sm py-1 px-2 rounded-lg whitespace-no-wrap'>$pagePath</span>";

                return "<div class='whitespace-no-wrap'>
                            <span class='bg-40 text-sm py-1 px-2 rounded-lg'>$pagePath</span>
                            <a target='_blank' href='$pageUrl' class='text-sm py-1 px-2 text-primary no-underline dim font-bold'>$buttonText</a>
                        </div>";
            })->asHtml()->exceptOnForms(),

            // Template selector
            Select::make(__od_npm('templateField'), 'template')
                ->options(fn () => $this->getTemplateOptions())
                ->displayUsingLabels(),
        ];

        if (isset($templateClass) && $templateClass::$seo) $fields[] = new Panel(__('novaPageManager.seo'), $this->getSeoFields());

        if (!empty($templateFieldsAndPanels)) {
            if (count($templateFieldsAndPanels['fields']) > 0) {
                $fields[] = new Panel(__('novaPageManager.pageData'), $templateFieldsAndPanels['fields']);
            }

            if (count($templateFieldsAndPanels['panels']) > 0) {
                $fields = array_merge($fields, $templateFieldsAndPanels['panels']);
            }
        }

        return $fields;
    }

    protected function getSeoFields()
    {
        $customSeoFields = NPM::getCustomSeoFields();
        if (!empty($customSeoFields)) return $customSeoFields;

        return [
            Text::make(__('novaPageManager.seoTitle'), 'seo_title')->hideFromIndex()->hideWhenCreating(),
            Text::make(__('novaPageManager.seoDescription'), 'seo_description')->hideFromIndex()->hideWhenCreating(),
            Image::make(__('novaPageManager.seoImage'), 'seo_image')->hideFromIndex()->hideWhenCreating()
        ];
    }

    protected function getTemplateOptions()
    {
        $templates = NPM::getPageTemplates();

        $options = [];
        foreach ($templates as $template) {
            $options[$template::$id] = $template::$name;
        }

        return $options;
    }

    public function title()
    {
        return $this->name . ' (' . $this->slug . ')';
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $table = NPM::getPagesTableName();

        $query->selectRaw("{$table}.*, CONCAT(COALESCE(p3.name, ''), COALESCE(p2.name, ''), COALESCE(p1.name, ''), COALESCE({$table}.name, '')) AS hierarchy_order")
            ->leftJoin("{$table} AS p1", 'p1.id', '=', "{$table}.parent_id")
            ->leftJoin("{$table} AS p2", 'p2.id', '=', 'p1.parent_id')
            ->leftJoin("{$table} AS p3", 'p3.id', '=', 'p2.parent_id')
            ->orderByRaw('hierarchy_order');

        return $query;
    }

    public static function label()
    {
        return __('novaPageManager.pages');
    }

    public static function singularLabel()
    {
        return __('novaPageManager.page');
    }
}
