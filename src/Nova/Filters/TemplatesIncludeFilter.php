<?php

namespace Outl1ne\PageManager\Nova\Filters;

use Laravel\Nova\Http\Requests\NovaRequest as Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Outl1ne\PageManager\NPM;

class TemplatesIncludeFilter extends TemplatesExcludeFilter
{
    public $name = 'Templates (include)';

    public function apply(Request $request, Builder $query, mixed $value) : Builder
    {
        if (empty($value)) return $query;
        return $query->whereIn('template', $value);
    }
}
