<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pemohon_id')->unsigned();
            $table->integer('room_id')->unsigned();
            $table->date('tanggal');
            $table->time('time_from');
            $table->time('time_to');  
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('pemohon_id')
            ->references('id')
            ->on('user')
            ->onDelete('restrict')
            ->onUpdate('cascade');
            $table->foreign('room_id')
            ->references('id')
            ->on('master_room')
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
        Schema::dropIfExists('booking');
    }
}
