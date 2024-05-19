<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expert_id')->unsigned();
            $table->timestamp('start_time')->nullable(); // Allows NULL values
            $table->timestamp('end_time')->nullable(); // Allows NULL values
            $table->integer('price')->unsigned()->default(200000);
            $table->text('describe');
            $table->softDeletes();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('calendars');
    }
};
