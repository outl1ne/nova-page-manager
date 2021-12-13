<?php

namespace OptimistDigital\NovaPageManager\Models;

use NovaPageManagerCache;
use OptimistDigital\NovaPageManager\NovaPageManager;

class Page extends TemplateModel
{
    protected $appends = [
        'path'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NovaPageManager::getPagesTableName());
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($template) {
            // Is a parent template
            if ($template->parent_id === null) {
                // Find child templates
                $childTemplates = NovaPageManager::getPageModel()::where('parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) return;

                // Set their parent to null
                $childTemplates->each(function ($template) {
                    $template->update(['parent_id' => null]);
                });
            }
        });

        static::updated(function () {
            NovaPageManagerCache::clear();
        });
    }

    public function parent()
    {
        return $this->belongsTo(NovaPageManager::getPageModel());
    }

    public function getParentAttribute()
    {
        if ($this->relationLoaded('parent')) {
            return $this->getRelationValue('parent');
        }

        $parent = NovaPageManagerCache::find($this->parent_id);

        $this->setRelation('parent', $parent);

        return $parent;
    }

    public function childDraft()
    {
        return $this->hasOne(NovaPageManager::getPageModel(), 'draft_parent_id', 'id');
    }

    public function getChildDraftAttribute()
    {
        if ($this->relationLoaded('child_draft')) {
            return $this->getRelationValue('child_draft');
        }

        $childDraft = NovaPageManagerCache::whereChildDraft($this->id);

        $this->setRelation('child_draft', $childDraft);

        return $childDraft;
    }

    public function localeParent()
    {
        return $this->belongsTo(NovaPageManager::getPageModel());
    }

    public function getLocaleParentAttribute()
    {
        if ($this->relationLoaded('locale_parent')) {
            return $this->getRelationValue('locale_parent');
        }

        $localeParent = NovaPageManagerCache::find($this->locale_parent_id);

        $this->setRelation('locale_parent', $localeParent);

        return $localeParent;
    }

    public function isDraft()
    {
        return isset($this->preview_token) ? true : false;
    }

    public function getPathAttribute()
    {
        $localeParent = $this->localeParent;
        $isLocaleChild = $localeParent !== null;
        $pathFinderPage = $isLocaleChild ? $localeParent : $this;
        if (!isset($pathFinderPage->parent)) return NovaPageManager::getPagePath($this, $this->normalizePath($this->slug));

        $parentSlugs = [];
        $parent = $pathFinderPage->parent;
        while (isset($parent)) {
            if ($isLocaleChild) {
                $localizedPage = NovaPageManager::getPageModel()::where('locale_parent_id', $parent->id)->where('locale', $this->locale)->first();
                $parentSlugs[] = $localizedPage !== null ? $localizedPage->slug : $parent->slug;
            } else {
                $parentSlugs[] = $parent->slug;
            }
            $parent = $parent->parent;
        }
        $parentSlugs = array_reverse($parentSlugs);

        $normalizedPath = $this->normalizePath(implode('/', $parentSlugs) . "/" . $this->slug);

        return NovaPageManager::getPagePath($this, $normalizedPath);
    }

    protected function normalizePath($path)
    {
        if (empty($path)) return null;
        if ($path[0] !== '/') $path = "/$path";
        if (strlen($path) > 1 && substr($path, -1) === '/') $path = substr($path, 0, -1);
        return preg_replace('/[\/]+/', '/', $path);
    }
}
