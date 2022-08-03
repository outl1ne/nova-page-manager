<?php

namespace Outl1ne\PageManager\Nova\Resources;

use Laravel\Nova\Panel;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Outl1ne\PageManager\NPM;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Outl1ne\PageManager\Template;
use Outl1ne\PageManager\Nova\Fields\PageLinkField;
use Outl1ne\PageManager\Nova\Fields\PrefixSlugField;
use Outl1ne\PageManager\Nova\Fields\PageManagerField;

class Page extends TemplateResource
{
    public static $title = 'name';
    public static $model = null;
    public static $displayInNavigation = false;
    public static $search = ['name', 'slug', 'template'];

    protected $type = 'page';



    // ------------------------------
    // Core resource setup
    // ------------------------------

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

    public function title()
    {
        return "{$this->name} ({$this->slug})";
    }

    public static function label()
    {
        return __('novaPageManager.pageResourceLabel');
    }

    public static function singularLabel()
    {
        return __('novaPageManager.pageResourceSingularLabel');
    }



    // ------------------------------
    // Fields
    // ------------------------------

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
                ->nullable()
                ->showOnPreview(),

            // Template selector
            Select::make(__('novaPageManager.templateField'), 'template')
                ->options(fn () => $this->getTemplateOptions(Template::TYPE_PAGE))
                ->rules('required', 'max:255')
                ->displayUsingLabels()
                ->showOnPreview(),

            // Name field
            Text::make(__('novaPageManager.nameField'), 'name')
                ->translatable(NPM::getLocales())
                ->rules('required', 'max:255')
                ->showOnPreview(),

            // Slug on form views
            PrefixSlugField::make(__('novaPageManager.slugField'), 'slug')
                ->translatable(NPM::getLocales())
                ->from('name.en')
                ->onlyOnForms()
                ->pathPrefix($pathPrefix)
                ->pathSuffix($pathSuffix)
                ->rules('required'),

            // Slug on index and detail views
            PageLinkField::make(__('novaPageManager.slugField'), 'path')
                ->exceptOnForms()
                ->withPageUrl(NPM::getBaseUrl($this->resource))
                ->translatable(NPM::getLocales())
                ->showOnPreview(),

            // Page data panel
            Panel::make(__('novaPageManager.pageFieldsPanelName'), [
                PageManagerField::make(\Outl1ne\PageManager\Template::TYPE_PAGE)
                    ->withTemplate($this->template)
                    ->withSeoFields(NPM::getSeoFields())
                    ->hideWhenCreating(),
            ])
        ];
    }



    // --------------------
    // Page Manager Helpers
    // --------------------

    public function getParentOptions()
    {
        $page = NPM::getPageModel();
        if ($this->id) {
            $pages = $page::query()
                ->where('id', '<>', $this->id)
                ->where(fn ($query) => $query
                    ->whereNull('parent_id')
                    ->orWhere('parent_id', '<>', $this->id))
                ->get();
        } else {
            $pages = $page::all();
        }
        return $pages->pluck('name', 'id');
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
                $localePrefix = implode('/', $explodedPath);
                $pathPrefix[$key] = !empty($localePrefix) ? "{$localePrefix}/" : null;
            }
        }

        return [$pathPrefix, $pathSuffix];
    }
}
