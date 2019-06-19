<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddDraftFieldsToPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('nova-page-manager.table', 'nova_page_manager');
        $pagesTableName = $tableName . '_pages';

        Schema::table($pagesTableName, function ($table) use ($pagesTableName) {
            $table->string('preview_token')->nullable();
            $table->boolean('published')->default(true);
            $table->bigInteger('draft_parent_id')->nullable()->unsigned();
            $table->foreign('draft_parent_id')->references('id')->on($pagesTableName)->onDelete('cascade');
            $table->dropUnique(['locale', 'slug']);
            $table->unique(['locale', 'slug', 'published']);

            $table->index('locale_parent_id');
            $table->dropUnique('nova_page_manager_locale_parent_id_locale_unique');
            $table->unique(['locale_parent_id', 'locale', 'published']);
            $table->dropIndex('nova_page_manager_pages_locale_parent_id_index');
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
        $pagesTableName = $tableName . '_pages';

        Schema::table($pagesTableName, function ($table) use ($pagesTableName) {
            $table->dropForeign($pagesTableName . '_draft_parent_id_foreign');
            $table->dropColumn('draft_parent_id');
            $table->dropColumn('published');
            $table->dropColumn('preview_token');
            $table->dropUnique(['locale', 'slug', 'published']);
            $table->unique(['locale', 'slug']);

            $table->index('locale_parent_id');
            $table->dropUnique(['locale_parent_id', 'locale', 'published']);
            $table->unique(['locale_parent_id', 'locale']);
            $table->dropIndex('nova_page_manager_pages_locale_parent_id_index');
        });
    }
}
