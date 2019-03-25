<?php

namespace OptimistDigital\NovaPageManager\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class TemplateChildrenFilter extends BooleanFilter
{
    public $name = 'Children filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if (isset($value['show_children']) && $value['show_children']) return $query;
        return $query->whereNull('parent_id');
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Show children' => 'show_children'
        ];
    }

    public function default()
    {
        return [
            'show_children' => false
        ];
    }
}
