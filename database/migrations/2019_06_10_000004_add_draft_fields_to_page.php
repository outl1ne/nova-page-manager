<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use OptimistDigital\NovaPageManager\NovaPageManager;

class AddDraftFieldsToPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pagesTableName = NovaPageManager::getPagesTableName();

        Schema::table($pagesTableName, function ($table) use ($pagesTableName) {
            $table->string('preview_token')->nullable();
            $table->boolean('published')->default(true);
            $table->bigInteger('draft_parent_id')->nullable()->unsigned();
            $table->foreign('draft_parent_id')->references('id')->on($pagesTableName)->onDelete('cascade');
            $table->dropUnique('nova_page_manager_locale_slug_unique');
            $table->unique(['locale', 'slug', 'published'], 'nova_page_manager_locale_slug_published_unique');

            $table->index('locale_parent_id', 'nova_page_manager_locale_parent_id_workaround_index'); // WORKAROUND FOR: Cannot drop index needed in a foreign key constraint
            $table->dropUnique('nova_page_manager_locale_parent_id_locale_unique');
            $table->unique(['locale_parent_id', 'locale', 'published'], 'nova_page_manager_locale_parent_id_locale_published_unique');
            $table->dropIndex('nova_page_manager_locale_parent_id_workaround_index'); // WORKAROUND FOR: Cannot drop index needed in a foreign key constraint
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

        Schema::table($pagesTableName, function ($table) use ($pagesTableName) {
            $table->dropForeign($pagesTableName . '_draft_parent_id_foreign');
            $table->dropColumn('draft_parent_id');
            $table->dropColumn('published');
            $table->dropColumn('preview_token');
            $table->dropUnique('nova_page_manager_locale_slug_published_unique');
            $table->unique(['locale', 'slug'], 'nova_page_manager_locale_slug_unique');

            $table->index('locale_parent_id', 'nova_page_manager_locale_parent_id_workaround_index'); // WORKAROUND FOR: Cannot drop index needed in a foreign key constraint
            $table->dropUnique('nova_page_manager_locale_parent_id_locale_published_unique');
            $table->unique(['locale_parent_id', 'locale'], 'nova_page_manager_locale_parent_id_locale_unique');
            $table->dropIndex('nova_page_manager_locale_parent_id_workaround_index'); // WORKAROUND FOR: Cannot drop index needed in a foreign key constraint
        });
    }
}
