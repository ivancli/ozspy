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
        Schema::create('web_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retailer_id')->unsigned()->nullable();
            $table->foreign('retailer_id')->references('id')->on('retailers')
                ->onUpdate('cascade')->onDelete('set null');
            $table->integer('web_category_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('field')->nullable();
            $table->string('url', 2083)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['retailer_id', 'web_category_id', 'name']);
        });

        Schema::table('web_categories', function (Blueprint $table) {
            $table->foreign('web_category_id')->references('id')->on('web_categories')
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
        Schema::dropIfExists('web_categories');
    }
}
