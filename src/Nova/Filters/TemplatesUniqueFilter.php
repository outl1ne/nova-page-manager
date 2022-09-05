<?php

namespace Outl1ne\PageManager\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Outl1ne\PageManager\NPM;

class TemplatesUniqueFilter extends Filter
{
    public $name = 'Unique';

    protected $type = 'pages';

    public function __construct(string $type = 'pages')
    {
        $this->type = $type;
    }

    public function apply(Request $request, $query, $value)
    {
        if (empty($value)) return $query;

        $uniqueTemplates = $this->getUniqueTemplates();

        if ($value === 'unique') return $query->whereIn('template', $uniqueTemplates);
        return $query->whereNotIn('template', $uniqueTemplates);
    }

    public function options(Request $request)
    {
        return [
            'Unique templates only' => 'unique',
            'Non-unique templates only' => 'non-unique',
        ];
    }

    protected function getUniqueTemplates()
    {
        $templates = $this->type === 'pages' ? NPM::getPageTemplates() : NPM::getRegionTemplates();
        $templates = array_filter($templates, fn ($t) => $t['unique'] ?? false);
        return array_keys($templates);
    }
}
