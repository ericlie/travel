<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inquiry_id')->unsigned();
            $table->foreign('inquiry_id')
                ->references('id')
                ->on('inquiries');
            $table->char('city_code', 3)->index();
            $table->string('name');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_days');
            $table->integer('total_rooms');
            $table->decimal('price', 24, 2)->unsigned()->default(0);
            $table->decimal('total_price', 24, 2)->unsigned()->default(0);
            $table->integer('popularity')->unsigned()->default(0);
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
        Schema::dropIfExists('hotel_quotations');
    }
}
