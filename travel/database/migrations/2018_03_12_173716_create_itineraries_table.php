<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inquiry_id')->unsigned();
            $table->foreign('inquiry_id')
                ->references('id')
                ->on('inquiries');
            $table->integer('quotation_id')->unsigned();
            $table->foreign('quotation_id')
                ->references('id')
                ->on('quotations');
            $table->integer('hotel_quotation_id')->unsigned();
            $table->foreign('hotel_quotation_id')
                ->references('id')
                ->on('hotel_quotations');
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
        Schema::dropIfExists('itineraries');
    }
}
