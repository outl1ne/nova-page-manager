<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRegionAndPagesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('nova-page-manager.table', 'nova_page_manager');
        $regionsTableName = $tableName . '_regions';
        $pagesTableName = $tableName . '_pages';

        // Create regions table
        Schema::create($regionsTableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->string('locale');
            $table->string('template');
            $table->bigInteger('locale_parent_id')->nullable();
            $table->json('data')->nullable();

            $table->unique(['locale', 'template'], 'nova_page_manager_locale_template');
        });

        // Move all regions from original table to new regions table
        DB::table($tableName)->where('type', 'region')->get()->each(function ($region) use ($tableName, $regionsTableName) {
            DB::table($regionsTableName)->insert([
                'id' => $region->id,
                'name' => $region->name,
                'locale' => $region->locale,
                'template' => $region->template,
                'locale_parent_id' => $region->locale_parent_id,
                'data' => $region->data,
                'created_at' => $region->created_at,
                'updated_at' => $region->updated_at,
            ]);
            DB::table($tableName)->where('id', $region->id)->delete();
        });

        // Rename pages table
        Schema::rename($tableName, $pagesTableName);

        // Drop deprecated type column
        Schema::table($pagesTableName, function ($table) {
            $table->dropColumn('type');
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
        $regionsTableName = $tableName . '_regions';
        $pagesTableName = $tableName . '_pages';

        // Not worth the effort to undo the massive amount of changes in "up"
        // as there's no usecase to undoing just this migration

        Schema::dropIfExists($regionsTableName);
        Schema::dropIfExists($pagesTableName);
    }
}
