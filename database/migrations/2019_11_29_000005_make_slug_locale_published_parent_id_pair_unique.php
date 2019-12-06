<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use OptimistDigital\NovaPageManager\NovaPageManager;

class MakeSlugLocalePublishedParentidPairUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pagesTableName = NovaPageManager::getPagesTableName();
        $key = DB::select(
            DB::raw('SHOW KEYS
            FROM nova_page_manager_pages
            WHERE Key_name LIKE "nova_page_manager_pages%"
            AND Key_name LIKE "%locale_slug_published_unique"')
        );
        $indexValue = empty($key) ? 'nova_page_manager' : 'nova_page_manager_pages';

        Schema::table($pagesTableName, function ($table) use ($indexValue) {
            $table->dropUnique("{$indexValue}_locale_slug_published_unique");
            $table->unique(['locale', 'slug', 'published', 'parent_id'], 'nova_page_manager_locale_slug_published_parent_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $pagesTableName = NovaPageManager::getPagesTableName();

        Schema::table($pagesTableName, function ($table) {
            $table->dropUnique('nova_page_manager_locale_slug_published_parent_id_unique');
            $table->unique(['locale', 'slug', 'published'], 'nova_page_manager_locale_slug_published_unique');
        });
    }
}
