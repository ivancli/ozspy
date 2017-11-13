<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrawlProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawl_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')
                ->onUpdate('cascade')->onDelete('set null');
            $table->integer('retailer_id')->unsigned()->nullable();
            $table->foreign('retailer_id')->references('id')->on('retailers')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('retailer_product_id')->nullable();
            $table->string('name');
            $table->float('price')->nullable();
            $table->string('model_number')->nullable();
            $table->string('sku')->nullable();
            $table->string('gtin')->nullable();
            $table->string('vpn')->nullable();
            $table->string('ean')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('crawl_products');
    }
}
