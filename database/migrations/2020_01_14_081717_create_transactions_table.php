<?php

use App\Models\enums\TransactionMedium;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->uuid('loan_id');
            $table->uuid('branch_id');
            $table->uuid('contribution_id');
            $table->uuid('transaction_initiator_id');
            $table->uuid('payment_request_id');
            $table->dateTime('transaction_date');
            $table->enum('transaction_type', [
                TransactionType::BRANCH_EXPENSE,
                TransactionType::BRANCH_FUND_DISBURSEMENT,
                TransactionType::CONTRIBUTION_PAYMENT,
                TransactionType::CONTRIBUTION_WITHDRAWAL,
                TransactionType::DEFAULT_CANCELLATION,
                TransactionType::DEFAULT_REPAYMENT,
                TransactionType::LOAN_DISBURSEMENT,
                TransactionType::LOAN_REPAYMENT,
                TransactionType::VENDOR_PAYOUT
            ]);
            $table->float('transaction_amount');
            $table->enum('transaction_medium', [
                TransactionMedium::CASH,
                TransactionMedium::ONLINE,
                TransactionMedium::BANK_TRANSFER,
                TransactionMedium::BANK_TELLER
            ]);
            $table->string('transaction_purpose');
            $table->enum('transaction_status', [
                TransactionStatus::PENDING,
                TransactionStatus::COMPLETED,
                TransactionStatus::FAILED
            ]);
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
        Schema::dropIfExists('transactions');
    }
}
