<?php

namespace Outl1ne\PageManager\Models;

use Illuminate\Support\Str;
use Outl1ne\PageManager\NPM;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    protected $fillable = ['parent_id'];
    protected $translatable = ['name', 'slug'];
    protected $casts = [
        'data' => 'array',
        'seo' => 'array',
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
            // TODO Handle children upon delete
        });
    }

    public function parent()
    {
        return $this->belongsTo(NPM::getPageModel());
    }

    public function getSlugWithSuffix($locale = null)
    {
        $path = $locale ? $this->getTranslation('slug', $locale) : $this->slug;

        $template = NPM::getPageTemplateBySlug($this->template);
        if ($template) {
            $templateClass = new $template['class'];
            if ($pathSuffix = $templateClass->pathSuffix()) $path = "{$path}/{$pathSuffix}";
        }

        return $this->normalizePath($path);
    }

    public function getPathAttribute()
    {
        $locales = NPM::getLocales();

        $localisedPaths = [];

        foreach ($locales as $key => $localeName) {
            $parentSlugs = [];
            $parent = $this->parent;
            while (isset($parent)) {
                $parentSlugs[] = $parent->getSlugWithSuffix($key);
                $parent = $parent->parent;
            }
            $parentSlugs = array_reverse($parentSlugs);

            $path = implode('/', $parentSlugs) . '/' . $this->getSlugWithSuffix($key);

            $localisedPaths[$key] = $this->normalizePath($path);
        }

        return $localisedPaths;
    }

    public function normalizePath($path)
    {
        if (empty($path)) return null;

        // Replace multiple consecutive / with just one
        $path = preg_replace('/[\/]+/', '/', $path);

        if (!Str::startsWith($path, '/')) $path = "/$path";
        if (Str::length($path) > 1 && Str::endsWith($path, '/')) $path = substr($path, 0, -1);

        return $path;
    }
}
