## Migrating from Nova 3 / Page Manager 4 to Nova 4 / Page Manager 5

### Modify template classes

To each template class, you need to add the following functions:

```php
// Name displayed in CMS
public function name(Request $request)
{
    return 'Page template name';
}

// Fields displayed in CMS
// Nothing changed here during the upgrade
public function fields(Request $request): array
{
    return [];
}

// Resolve data for serialization
public function resolve($page): array
{
    // Modify data as you please (ie turn ID-s into models)
    return $page->data;
}
```

### Replace resolveResponseUsing with resolve

If you previosuly used the `resolveResponseUsing()` helper on the fields, you must remove that function and replace it with manual data retrieval inside the template's `resolve` function.

### Migration

This is a working migration that migrates pages from Page Manager 4 from Page Manager 5.

```php
<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Outl1ne\PageManager\Models\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    const OLD_PAGES_TABLE_NAME = 'nova_page_manager_pages';

    public function up()
    {
        $pageStructure = $this->getExistingPages();

        foreach ($pageStructure as $rootPageItem) {
            $this->createPageFromFormattedPageStructureItem($rootPageItem);
        }

        // Optionally drop old table
        Schema::dropIfExists(static::OLD_PAGES_TABLE_NAME);
    }

    public function down()
    {
        // Theoretically possible, but not worth the effort
    }

    protected function createPageFromFormattedPageStructureItem($pageItem, $parentId = null)
    {
        $newPage = new Page();
        $newPage->active = true;
        $newPage->parent_id = $parentId;
        $newPage->created_at = $pageItem['created_at'];
        $newPage->updated_at = $pageItem['updated_at'];
        $newPage->template = $pageItem['template'];
        $newPage->setTranslations('name', $pageItem['name'] ?? []);
        $newPage->setTranslations('slug', $pageItem['slug'] ?? []);
        $newPage->data = $pageItem['data'] ?? [];
        $newPage->seo = $pageItem['seo'] ?? [];
        $newPage->save();
        $newPage->refresh();

        foreach ($pageItem['children'] ?? [] as $childPage) {
            $this->createPageFromFormattedPageStructureItem($childPage, $newPage->id);
        }
    }

    protected function getExistingPages()
    {
        $formatPages = function (Collection $pages) use (&$formatPages) {
            $allPages = [];
            $pages->each(function ($page) use (&$allPages, &$formatPages) {
                $localeChildren = DB::table(static::OLD_PAGES_TABLE_NAME)->where('locale_parent_id', $page->id)->get();

                $_pages = collect([$page, $localeChildren])->flatten();

                $seoData = [];
                $locales = $_pages->pluck('locale')->toArray();
                foreach ($locales as $locale) {
                    $seoData[$locale] = [];
                    $seoData[$locale]['title'] = $_pages->pluck('seo_title', 'locale')->toArray()[$locale] ?? null;
                    $seoData[$locale]['description'] = $_pages->pluck('seo_description', 'locale')->toArray()[$locale] ?? null;
                    $seoData[$locale]['image'] = $_pages->pluck('seo_image', 'locale')->toArray()[$locale] ?? null;
                }

                $_data = [
                    'id' => $_pages->pluck('id', 'locale')->toArray(),
                    'locales' => $locales,
                    'created_at' => $_pages->max('created_at'),
                    'updated_at' => $_pages->max('updated_at'),
                    'name' => $_pages->pluck('name', 'locale')->toArray(),
                    'slug' => $_pages->pluck('slug', 'locale')->toArray(),
                    'data' => $_pages->pluck('data', 'locale')->map(fn ($data) => json_decode($data, true))->toArray(),
                    'seo' => $seoData,
                    'template' => $page->template,
                ];

                $children = DB::table(static::OLD_PAGES_TABLE_NAME)->where('parent_id', $page->id)->get();
                $_data['children'] = $formatPages($children);

                $allPages[] = $_data;
            });
            return $allPages;
        };

        $parentPages = DB::table(static::OLD_PAGES_TABLE_NAME)->whereNull('locale_parent_id')->get();
        return $formatPages($parentPages);
    }
};
```
