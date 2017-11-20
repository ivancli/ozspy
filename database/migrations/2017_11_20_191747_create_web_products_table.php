<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('retailer_id')->unsigned();
            $table->foreign('retailer_id')->references('id')->on('retailers')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')
                ->onUpdate('cascade')->onDelete('set null');
            $table->string('retailer_product_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('url', 2083)->nullable();
            $table->string('model')->nullable();
            $table->string('sku')->nullable();
            $table->string('gtin8', 8)->nullable();
            $table->string('gtin12', 12)->nullable();
            $table->string('gtin13', 13)->nullable();
            $table->string('gtin14', 14)->nullable();
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
        Schema::dropIfExists('web_products');
    }
}
