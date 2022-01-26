<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkedCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parked_cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parking_slot_id')->index();
            $table->string('car_plate')->index();
            $table->dateTime('parked_at')->nullable();
            $table->dateTime('unparked_at')->nullable();
            $table->boolean('is_continuous')->default(false);
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
        Schema::dropIfExists('parked_cars');
    }
}
