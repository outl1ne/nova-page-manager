<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddChildParentRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('nova-page-manager.table', 'nova_page_manager');

        // Make "type" enum column into string
        Schema::table($tableName, function (Blueprint $table) {
            $table->string('type_temp', 50);
        });

        // Copy "type" to "type_temp"
        DB::table($tableName)->get()->each(function ($model) {
            $model->update(['type_temp' => $model->type]);
        });

        // Delete "type" and rename "type_temp" to "temp"
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->renameColumn('type_temp', 'type');
        });

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            $table->dropUnique('nova_page_manager_parent_id_locale_unique');

            // Rename "parent_id" to "locale_parent_id"
            $table->renameColumn('parent_id', 'locale_parent_id');
        });

        // Run as separate transaction after rename
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            $table->bigInteger('locale_parent_id')->nullable()->unsigned()->change();
            $table->foreign('locale_parent_id')->references('id')->on($tableName);

            // New "parent_id" column
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->foreign('parent_id')->references('id')->on($tableName);

            // Force "locale_parent_id <-> locale" pair to be unique
            $table->unique(['locale_parent_id', 'locale'], 'nova_page_manager_locale_parent_id_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = config('nova-page-manager.table', 'nova_page_manager');

        // Not worth the effort to undo the massive amount of changes in "up"
        // as there's no usecase to undoing just this migration

        Schema::dropIfExists($table);
    }
}
