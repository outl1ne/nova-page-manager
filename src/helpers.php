<?php
use OptimistDigital\NovaPageManager\Models\Page;

if (!function_exists('nova_get_pages')) {
    function nova_get_pages()
    {
        $parentPages = Page::whereNull('parent_id')->whereNull('locale_parent_id')->get();
        // dd($parentPages);
        return $parentPages;
    }
}
