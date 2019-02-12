<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GuestlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guestlist', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booking_id')->unsigned();
            $table->string('name');
            $table->string('email');
            $table->boolean('rsvp')->default(0);;
            $table->integer('reminder')->nullable();
            $table->boolean('attend')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('booking_id')
            ->references('id')
            ->on('booking')
            ->onDelete('restrict')
            ->onUpdate('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guestlist');
    }
}
