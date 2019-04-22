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
        if (isset($value['show_child_locales']) && $value['show_child_locales']) return $query;
        return $query->whereNull('locale_parent_id');
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
            'Show child locales' => 'show_child_locales'
        ];
    }

    public function default()
    {
        return [
            'show_children' => false
        ];
    }
}
