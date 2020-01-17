<?php

use App\Models\enums\TransactionProcessingActions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessedTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processed_transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('causer_id');
            $table->uuid('transaction_id');
            $table->enum('processing_type', [
                TransactionProcessingActions::APPROVE,
                TransactionProcessingActions::DISAPPROVE
            ]);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('causer_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processed_transactions');
    }
}
