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

        Schema::table($pagesTableName, function (Blueprint $table) use ($pagesTableName) {
            $table->json('name')->change();
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

        Schema::table($pagesTableName, function (Blueprint $table) use ($pagesTableName) {
            $table->string('name', 255)->change();
        });
    }
};
