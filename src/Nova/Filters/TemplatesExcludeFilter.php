<?php

namespace Outl1ne\PageManager\Nova\Filters;

use Laravel\Nova\Http\Requests\NovaRequest as Request;
use Outl1ne\PageManager\NPM;
use Outl1ne\NovaMultiselectFilter\MultiselectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;

class TemplatesExcludeFilter extends MultiselectFilter
{
    public $name = 'Templates (exclude)';

    protected $type = 'pages';

    public function type(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function apply(Request $request, Builder $query, mixed $value) : Builder
    {
        if (empty($value)) return $query;
        return $query->whereNotIn('template', $value);
    }

    public function options(Request $request)
    {
        return $this->getTemplateOptions();
    }

    protected function getTemplateOptions()
    {
        $templates = $this->type === 'pages' ? NPM::getPageTemplates() : NPM::getRegionTemplates();
        $options = [];
        foreach ($templates as $slug => $template) {
            $options[$slug] = (new $template['class'])->name(request());
        }
        return $options;
    }
}
