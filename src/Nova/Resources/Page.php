<?php

namespace Outl1ne\PageManager\Nova\Resources;

use Laravel\Nova\Panel;
use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Outl1ne\PageManager\Nova\Fields\PageLinkField;
use Outl1ne\PageManager\Nova\Fields\PageManagerField;
use Outl1ne\PageManager\Nova\Fields\PrefixSlugField;

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

    public function getParentOptions()
    {
        $page = NPM::getPageModel();
        if ($this->id) {
            $pages = $page::whereNot('id', '<=>', $this->id)->whereNot('parent_id', '<=>', $this->id)->get();
        } else {
            $pages = $page::all();
        }
        return $pages->pluck('name', 'id');
    }

    public function fields(Request $request)
    {
        [$pathPrefix, $pathSuffix] = $this->getPathPrefixAndSuffix();

        return [
            // Parent selector
            Select::make('Parent page', 'parent_id')
                ->options($this->getParentOptions())
                ->hideFromIndex()
                ->hideFromDetail()
                ->displayUsingLabels()
                ->nullable(),

            // Template selector
            Select::make(__('novaPageManager.templateField'), 'template')
                ->options(fn () => $this->getTemplateOptions())
                ->rules('required', 'max:255')
                ->displayUsingLabels(),

            // Name field
            Text::make(__('novaPageManager.nameField'), 'name')
                ->translatable(NPM::getLocales())
                ->rules('required', 'max:255'),

            // Slug on form views
            PrefixSlugField::make(__('novaPageManager.slugField'), 'slug')
                ->translatable(NPM::getLocales())
                ->from('name.en')
                ->onlyOnForms()
                ->pathSuffix($pathSuffix)
                ->rules('required'),

            // Slug on index and detail views
            PageLinkField::make(__('novaPageManager.slugField'), 'path')
                ->exceptOnForms()
                ->baseUrl(NPM::getPageUrl())
                ->translatable(NPM::getLocales()),

            // Page data panel
            Panel::make(__('novaPageManager.pageFieldsPanelName'), [
                PageManagerField::make(\Outl1ne\PageManager\Template::TYPE_PAGE)
                    ->hideWhenCreating(),
            ])
        ];
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

    protected function getPathPrefixAndSuffix()
    {
        $pathPrefix = []; // translatable
        $pathSuffix = null;

        if ($this->id) {
            $path = $this->path ?? [];
            $locales = NPM::getLocales();
            $pathSuffix = $this->template->pathSuffix();

            foreach ($locales as $key => $localeName) {
                // Explode path and remove page's own path + suffix if it has one
                $explodedPath = explode('/', $path[$key]);
                if (!empty($pathSuffix)) array_pop($explodedPath); // Remove suffix
                array_pop($explodedPath); // Remove own path
                $pathPrefix[$key] = implode('/', $explodedPath) . '/';
            }
        }

        return [$pathPrefix, $pathSuffix];
    }

    protected function getTemplateOptions()
    {
        $templates = NPM::getPageTemplates();

        $options = [];
        foreach ($templates as $slug => $template) {
            $options[$slug] = (new $template['class'])->name(request());
        }

        return $options;
    }

    public function title()
    {
        return $this->name . ' (' . $this->slug . ')';
    }

    public static function label()
    {
        return __('novaPageManager.pageResourceLabel');
    }

    public static function singularLabel()
    {
        return __('novaPageManager.pageResourceSingularLabel');
    }
}
