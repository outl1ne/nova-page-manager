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
        Schema::create('nova_page_manager', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->enum('type', ['page', 'region']);
            $table->string('name');
            $table->string('slug')->default('')->unique();
            $table->string('locale');
            $table->string('template');
            $table->json('data')->nullable();

            $table->unique(['locale', 'template']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nova_page_manager_data');
    }
}
