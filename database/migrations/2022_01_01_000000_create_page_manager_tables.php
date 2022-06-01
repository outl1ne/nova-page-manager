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
        $regionsTableName = NPM::getRegionsTableName();

        // Create pages table
        Schema::create($pagesTableName, function (Blueprint $table) use ($pagesTableName) {
            $table->id();

            // Active status
            $table->boolean('active')->default(true);

            // Parent ID
            $table->unsignedBigInteger('parent_id')->nullable();

            // Template class
            $table->string('template')->nullable(false);

            $table->string('name', 255);
            $table->json('slug')->nullable(); // Translatable slug
            $table->json('seo')->nullable(); // Translatable SEO data
            $table->json('data')->nullable(); // Translatable page data

            // created_at, updated_at
            $table->timestamps();

            // Foreign key
            $table->foreign('parent_id')->references('id')->on($pagesTableName);
        });

        // Create regions table
        Schema::create($regionsTableName, function (Blueprint $table) {
            $table->id();

            // Template class
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
