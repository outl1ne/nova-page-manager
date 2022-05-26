<?php

namespace Outl1ne\NovaPageManager\Models;

use NPMCache;
use Outl1ne\NovaPageManager\NPM;
use Spatie\Translatable\HasTranslations;

class Page extends TemplateModel
{
    use HasTranslations;

    protected $translatable = ['slug'];

    protected $appends = [];

    protected $casts = [
        'data' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NPM::getPagesTableName());
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($template) {
            // Is a parent template
            if ($template->parent_id === null) {
                // Find child templates
                $childTemplates = NPM::getPageModel()::where('parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) return;

                // Set their parent to null
                $childTemplates->each(function ($template) {
                    $template->update(['parent_id' => null]);
                });
            }
        });

        static::updated(function () {
            NPMCache::clear();
        });
    }

    public function parent()
    {
        return $this->belongsTo(NPM::getPageModel());
    }

    public function getParentAttribute()
    {
        if ($this->relationLoaded('parent')) {
            return $this->getRelationValue('parent');
        }

        $parent = NPMCache::find($this->parent_id);

        $this->setRelation('parent', $parent);

        return $parent;
    }

    // public function getPathAttribute()
    // {
    //     $localeParent = $this->localeParent;
    //     $isLocaleChild = $localeParent !== null;
    //     $pathFinderPage = $isLocaleChild ? $localeParent : $this;
    //     if (!isset($pathFinderPage->parent)) return NPM::getPagePath($this, $this->normalizePath($this->slug));

    //     $parentSlugs = [];
    //     $parent = $pathFinderPage->parent;
    //     while (isset($parent)) {
    //         $parentSlugs[] = $parent->slug;
    //         $parent = $parent->parent;
    //     }
    //     $parentSlugs = array_reverse($parentSlugs);

    //     $normalizedPath = $this->normalizePath(implode('/', $parentSlugs) . "/" . $this->slug);

    //     return NPM::getPagePath($this, $normalizedPath);
    // }

    // protected function normalizePath($path)
    // {
    //     if (empty($path)) return null;
    //     if ($path[0] !== '/') $path = "/$path";
    //     if (strlen($path) > 1 && substr($path, -1) === '/') $path = substr($path, 0, -1);
    //     return preg_replace('/[\/]+/', '/', $path);
    // }
}
