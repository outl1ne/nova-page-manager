<?php

use Illuminate\Support\Facades\Schema;
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
        $table = config('nova-page-manager.table', 'nova_page_manager');

        Schema::create($table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->enum('type', ['page', 'region']);
            $table->string('name');
            $table->string('slug')->default('')->unique('nova_page_manager_slug_unique');
            $table->string('locale');
            $table->string('template');
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->json('data')->nullable();

            $table->unique(['parent_id', 'locale'], 'nova_page_manager_parent_id_locale_unique');
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
