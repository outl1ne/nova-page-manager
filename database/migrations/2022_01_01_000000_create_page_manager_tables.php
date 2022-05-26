<?php

use Outl1ne\NovaPageManager\NPM;
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
        $regionsTableName = NPM::getRegionsTableName();

        // Create pages table
        Schema::create($pagesTableName, function (Blueprint $table) {
            $table->bigIncrements('id');

            // Active status
            $table->boolean('active')->default(true);

            // Parent ID
            $table->bigInteger('parent_id')->nullable();

            // Page, region, template
            $table->string('template')->nullable(false);

            $table->string('name', 255);
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

            $table->string('name', 255);
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
        Schema::dropIfExists(NPM::getPagesTableName());
        Schema::dropIfExists(NPM::getRegionsTableName());
    }
};
