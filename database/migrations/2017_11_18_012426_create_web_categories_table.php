<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retailer_id')->unsigned()->nullable();
            $table->foreign('retailer_id')->references('id')->on('retailers')
                ->onUpdate('cascade')->onDelete('set null');
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('url', 2083);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['retailer_id', 'category_id', 'name']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
