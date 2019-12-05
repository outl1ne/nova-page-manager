<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class MakeSlugLocalePairUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('nova-page-manager.table', 'nova_page_manager');
        $pagesTableName = $tableName.'_pages';

        Schema::table($pagesTableName, function ($table) {
            $table->dropUnique('nova_page_manager_slug_unique');
            $table->unique(['locale', 'slug'], 'nova_page_manager_locale_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = config('nova-page-manager.table', 'nova_page_manager');
        $pagesTableName = $tableName.'_pages';

        Schema::table($pagesTableName, function ($table) {
            $table->dropUnique('nova_page_manager_locale_slug_unique');
            $table->unique('slug', 'nova_page_manager_slug_unique');
        });
    }
}
