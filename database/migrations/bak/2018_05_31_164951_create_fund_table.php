<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tenure_id');
            $table->unsignedInteger('address_id')->nullable();
            $table->text('objective')->nullable();
            $table->decimal('target_amount', 10, 2)->nullable();
            $table->decimal('estimated_expenses', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('meeting_type_id')->nullable();
            $table->date('resolved_at')->nullable();
            $table->unsignedInteger('quorum')->nullable();
            $table->text('method')->nullable();
            $table->date('applied_at')->nullable();
            $table->unsignedInteger('filing_status_id')->default(1);
            $table->boolean('is_editable')->default(1);
            $table->unsignedInteger('created_by_user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('tenure_id')
                ->references('id')
                ->on('tenure')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('address_id')
                ->references('id')
                ->on('address')
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
    public function down()
    {
        Schema::dropIfExists('fund');
    }
}
