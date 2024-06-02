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
        Schema::create('expert_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // user_id là khóa chính
            $table->text('experience')->default('I graduated from University of Oxford United Kingdom');
            $table->text('certificate')->default('https://png.pngtree.com/thumb_back/fh260/background/20230511/pngtree-nature-background-sunset-wallpaer-with-beautiful-flower-farms-image_2592160.jpg');
            $table->decimal('average_rating', 2, 1)->default(5.0);
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('expert_details');
    }
};
