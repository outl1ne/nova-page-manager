<?php

namespace OptimistDigital\NovaPageManager\Models;

use OptimistDigital\NovaPageManager\NovaPageManager;

class Page extends TemplateModel
{
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
                $childTemplates = Page::where('parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) return;

                // Set their parent to null
                $childTemplates->each(function ($template) {
                    $template->update(['parent_id' => null]);
                });
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Page::class);
    }
}
