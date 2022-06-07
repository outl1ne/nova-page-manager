<?php

namespace Outl1ne\PageManager\Models;

use NPMCache;
use Illuminate\Support\Str;
use Outl1ne\PageManager\NPM;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations;

    protected $fillable = ['parent_id'];
    protected $translatable = ['name', 'slug'];
    protected $casts = ['data' => 'array'];

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

    public function getPathAttribute()
    {
        $parentSlugs = [];
        $parent = $this->parent;
        while (isset($parent)) {
            $parentSlugs[] = $parent->slug;
            $parent = $parent->parent;
        }
        $parentSlugs = array_reverse($parentSlugs);

        return $this->normalizePath(implode('/', $parentSlugs) . '/' . $this->slug);
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
