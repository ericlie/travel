<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inquiry_id')->unsigned();
            $table->foreign('inquiry_id')
                ->references('id')
                ->on('inquiries');
            $table->char('origin', 3);
            $table->char('destination', 3);
            $table->char('airline_code', 3);
            $table->char('flight_no', 4);
            $table->integer('transfer')->unsigned()->default(0);
            $table->timestamp('departure_at')->nullable();
            $table->timestamp('return_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->decimal('distance', 24, 2)->unsigned()->default(0);
            $table->decimal('price', 24, 2)->unsigned()->default(0);
            $table->decimal('price_per_distance', 24, 2)->unsigned()->default(0);
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
        Schema::dropIfExists('quotations');
    }
}
