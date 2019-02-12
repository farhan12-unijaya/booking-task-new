<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_room', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('master_room_category_id')->unsigned();
            $table->string('name');
            $table->text('keterangan');
            $table->integer('price_non_holiday');
            $table->integer('price_holiday');
            $table->string('image');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('master_room_category_id')
            ->references('id')
            ->on('master_room_category')
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
        Schema::dropIfExists('master_room');
    }
}
