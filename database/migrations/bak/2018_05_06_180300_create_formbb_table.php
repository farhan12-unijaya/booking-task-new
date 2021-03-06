<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormbbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formbb', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tenure_id');
            $table->unsignedInteger('user_federation_id');
            $table->unsignedInteger('address_id')->nullable();
            $table->unsignedInteger('federation_type_id')->nullable();
            $table->unsignedInteger('sector_id')->nullable();
            $table->unsignedInteger('sector_category_id')->nullable();
            $table->string('sector_category')->nullable();
            $table->boolean('is_national')->nullable();
			$table->unsignedInteger('meeting_type_id')->nullable();
            $table->date('resolved_at')->nullable();
            $table->unsignedInteger('filing_status_id')->default(1);
            $table->date('applied_at')->nullable();
            $table->boolean('is_editable')->default(1);
            $table->unsignedInteger('created_by_user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('tenure_id')
                ->references('id')
                ->on('tenure')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('user_federation_id')
                ->references('id')
                ->on('user_federation')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('address_id')
                ->references('id')
                ->on('address')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('federation_type_id')
                ->references('id')
                ->on('master_federation_type')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('sector_id')
                ->references('id')
                ->on('master_sector')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('sector_category_id')
                ->references('id')
                ->on('master_sector_category')
                ->onDelete('restrict')
                ->onUpdate('cascade');

			$table->foreign('meeting_type_id')
                ->references('id')
                ->on('master_meeting_type')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('filing_status_id')
                ->references('id')
                ->on('master_filing_status')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('created_by_user_id')
                ->references('id')
                ->on('user')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('formbb');
    }
}
