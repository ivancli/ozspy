<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('official_name');
            $table->string('cca2')->nullable();
            $table->string('cca3')->nullable();
            $table->string('ccn3')->nullable();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
            $table->index('official_name');
            $table->index('region');
            $table->index('subregion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
