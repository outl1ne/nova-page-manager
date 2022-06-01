<?php

namespace Outl1ne\PageManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Outl1ne\PageManager\NPM;
use Outl1ne\PageManager\Template;

class PageManagerController extends Controller
{
    public function getFields(Request $request, $type, $slug)
    {
        $templates = $type === Template::TYPE_PAGE
            ? NPM::getPageTemplates()
            : NPM::getRegionTemplates();

        $template = $templates[$slug] ?? null;
        if (!$template) return response('Template not found.', 404);

        $templateClass = new $template['class'];
        $fields = $templateClass->fields($request);

        return [
            'fields' => $fields,
        ];
    }
}
