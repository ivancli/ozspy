<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebHistoricalPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_historical_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('web_product_id')->unsigned()->nullable();
            $table->foreign('web_product_id')->references('id')->on('web_products')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->float('amount');
            $table->timestamps();

            $table->index('created_at');
            $table->index(['web_product_id', 'created_at']);
            $table->index(['id', 'web_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_historical_prices');
    }
}
