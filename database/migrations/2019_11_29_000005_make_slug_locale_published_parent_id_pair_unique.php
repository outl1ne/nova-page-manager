<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use OptimistDigital\NovaPageManager\NovaPageManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        Schema::table($pagesTableName, function (Blueprint $table) use ($pagesTableName) {
            $keys = DB::connection()->getDoctrineSchemaManager()->listTableIndexes($pagesTableName);
            $keys = array_keys($keys);
            $uniqueKey = array_filter($keys, function ($key) {
                return Str::contains($key, 'locale_slug_published_unique');
            });
            $uniqueKey = $uniqueKey[0] ?? null;
            if ($uniqueKey) $table->dropUnique($uniqueKey);

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
