<?php

use Illuminate\Support\Facades\Schema;
use OptimistDigital\NovaPageManager\NPM;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageManagerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pagesTableName = NPM::getPagesTableName();
        $regionsTableName = NPM::getRegionsTableName();

        // Create pages table
        Schema::create($pagesTableName, function (Blueprint $table) {
            $table->bigIncrements('id');

            // Parent ID
            $table->bigInteger('parent_id')->nullable();

            // Page, region, template
            $table->string('template')->nullable(false);

            $table->json('name')->nullable(); // Translatable name
            $table->json('slug')->nullable(); // Translatable slug
            $table->json('seo')->nullable(); // Translatable and modifiable SEO data
            $table->json('data')->nullable(); // Translatable and modifiable page data

            // Created at, updated at
            $table->timestamps();
        });

        // Create regions table
        Schema::create($regionsTableName, function (Blueprint $table) {
            $table->bigIncrements('id');

            // Page, region, template
            $table->string('template')->nullable(false);

            $table->json('name')->nullable(); // Translatable name
            $table->json('data')->nullable(); // Translatable and modifiable page data

            // Created at, updated at
            $table->timestamps();
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

        Schema::dropIfExists($table);
    }
}
