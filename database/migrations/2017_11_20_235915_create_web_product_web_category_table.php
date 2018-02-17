<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebProductWebCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_product_web_category', function (Blueprint $table) {
            $table->integer('web_product_id')->unsigned();
            $table->integer('web_category_id')->unsigned();
            $table->timestamps();
            $table->primary(['web_product_id', 'web_category_id']);
            $table->foreign('web_product_id')->references('id')->on('web_products')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('web_category_id')->references('id')->on('web_categories')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->index(['web_product_id']);
            $table->index(['web_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_product_web_category');
    }
}
