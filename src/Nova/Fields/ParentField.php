<?php

namespace OptimistDigital\NovaPageManager\Nova\Fields;

use Laravel\Nova\Fields\Field;
use OptimistDigital\NovaPageManager\Models\Page;

class ParentField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'parent-field';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, 'parent_id', $resolveCallback);

        $options = [];

        Page
            ::whereNull('locale_parent_id')
            ->where('published', true)
            ->get()
            ->each(function ($page) use (&$options) {
                $options[$page->id] = $page->name . ' (' . $page->slug . ')';
            });

        $this->withMeta([
            'asHtml' => true,
            'options' => $options,
        ]);

        $optionKeys = array_keys($options);
        $this->rules('nullable', 'in:' . implode(',', $optionKeys));
    }

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);

        $options = $this->meta['options'];

        if (isset($resource->id)) {
            $excluded = $this->findExcludedChildAndParentPages($resource);
            $excludedIds = array_map(function ($page) {
                return $page['id'];
            }, $excluded);

            $options = array_filter(
                $options,
                function ($key) use ($excludedIds) {
                    return !in_array($key, $excludedIds);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        $parent = null;
        if (isset($resource->parent_id)) {
            $parentPage = Page::find($resource->parent_id);
            $parent = [
                'name' => $parentPage->name,
                'slug' => $parentPage->slug,
            ];
        }

        $this->withMeta([
            'canHaveParent' => empty($resource->locale_parent_id),
            'options' => $options,
            'parent' => $parent,
        ]);
    }

    public function findExcludedChildAndParentPages($page)
    {
        // Always exclude the current page as being your own parent is a paradox
        $childrenAndParents = [$page];

        // All parent's parents
        if (isset($page->parent_id)) {
            $_current = Page::find($page->parent_id);
            while (isset($_current->parent_id)) {
                $_current = Page::find($_current->parent_id);
                $childrenAndParents[] = $_current;
            }
        }

        // All children
        $childPages = Page::where('parent_id', $page->id)->get();

        while (sizeof($childPages) > 0) {
            $childrenAndParents = array_merge($childrenAndParents, $childPages->toArray());
            $childPages = Page::whereIn('parent_id', $childPages->map(function ($childPage) {
                return $childPage->id;
            }))->get();
        }

        return $childrenAndParents;
    }
}
