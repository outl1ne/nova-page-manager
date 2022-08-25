<?php

use Outl1ne\PageManager\NPM;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $pagesTableName = NPM::getPagesTableName();
        $pageModel = NPM::getPageModel();

        Schema::table($pagesTableName, function (Blueprint $table) {
            $table->renameColumn('name', 'name_string');
        });

        Schema::table($pagesTableName, function (Blueprint $table) {
            $table->json('name')->nullable();
        });

        $pageModel::all()->each(function ($page) {
            $page->name = json_decode($page->name_string, true);
            $page->save();
        });

        Schema::table($pagesTableName, function (Blueprint $table) {
            $table->dropColumn('name_string');
            $table->json('name')->after('template')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $pagesTableName = NPM::getPagesTableName();
        $pageModel = NPM::getPageModel();

        Schema::table($pagesTableName, function (Blueprint $table) {
            $table->renameColumn('name', 'name_json');
        });

        Schema::table($pagesTableName, function (Blueprint $table) {
            $table->string('name', 255)->nullable();
        });

        $pageModel::all()->each(function ($page) {
            $page->name = json_decode($page->name_json, true);
            $page->save();
        });

        Schema::table($pagesTableName, function (Blueprint $table) {
            $table->dropColumn('name_json');
            $table->string('name', 255)->after('template')->nullable(false)->change();
        });
    }
};
