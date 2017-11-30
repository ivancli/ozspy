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
            $table->integer('retailer_id')->unsigned()->nullable();
            $table->foreign('retailer_id')->references('id')->on('retailers')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('retailer_product_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('url', 2083)->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('sku')->nullable();
            $table->string('gtin8', 8)->nullable();
            $table->string('gtin12', 12)->nullable();
            $table->string('gtin13', 13)->nullable();
            $table->string('gtin14', 14)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
            $table->index(['name', 'retailer_id']);
            $table->index('slug');
            $table->index(['slug', 'retailer_id']);
            $table->index('brand');
            $table->index(['brand', 'retailer_id']);
            $table->index('model');
            $table->index(['model', 'retailer_id']);
            $table->index('sku');
            $table->index(['sku', 'retailer_id']);
        });

        DB::statement('ALTER TABLE web_products ADD FULLTEXT full(name)');
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
