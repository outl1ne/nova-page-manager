<?php

namespace OptimistDigital\NovaPageManager\Http\Controllers;

use Illuminate\Routing\Controller;
use OptimistDigital\NovaPageManager\Models\Page;
use OptimistDigital\NovaPageManager\NovaPageManager;

class PageController extends Controller {

    public function publishPage($pageId) {
        $pageToPublish = Page::find($pageId);
        
        if (isset($pageToPublish)) {

            if (isset($pageToPublish->draftParent)) {
                $publishedPage = $pageToPublish->draftParent;
                $publishedPage->data = $pageToPublish->data;
                $publishedPage->published = true;
                $publishedPage->save();
                $pageToPublish->delete();
                return $publishedPage;
            } else {
                $pageToPublish->published = true;
                $pageToPublish->preview_token = null;
                $pageToPublish->save();
                return $pageToPublish;
            }
        }

        return $pageToPublish;
    }
}
