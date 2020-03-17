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
        Schema::create('transaction', function (Blueprint $table) {
            $table->uuid('id');
            $table->dateTime('transaction_date');
            $table->enum('transaction_type', [
                TransactionType::BRANCH_EXPENSE,
                TransactionType::BRANCH_FUND_DISBURSEMENT,
                TransactionType::CONTRIBUTION_PAYMENT,
                TransactionType::DEFAULT_CANCELLATION,
                TransactionType::DEFAULT_REPAYMENT,
                TransactionType::LOAN_DISBURSEMENT,
                TransactionType::LOAN_REPAYMENT,
                TransactionType::VENDOR_PAYOUT,
                TransactionType::WALLET_PAYMENT,
                TransactionType::WALLET_WITHDRAWAL
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
            $table->uuid('owner_id');
            $table->string('owner_type');
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
        Schema::dropIfExists('transaction');
    }
}
