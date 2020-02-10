<?php

use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\Enums\LoanRepaymentFrequency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->string('loan_identifier')->unique();
            $table->string('loan_purpose');
            $table->string('loan_repayment_source');
            $table->float('loan_amount');
            $table->float('interest_rate')
                ->comment("Interest rate measured in percentages");
            $table->enum('loan_repayment_frequency', [
                LoanRepaymentFrequency::WEEKLY,
                LoanRepaymentFrequency::MONTHLY
            ]);
            $table->float('loan_balance')->nullable(); //
            $table->date('next_due_payment')->nullable();
            $table->date('due_date')->nullable();
            $table->float('service_charge')
                ->comment("percent of the loan / static amount");
            $table->float("default_amount")
                ->comment("static amount to be set for defaults on loans, defaults count when due date is exceeded");
            $table->integer("tenure")
                ->comment("Measured in months");
            $table->enum("disbursement_status", [
                DisbursementStatus::DISBURSED,
                DisbursementStatus::NOT_DISBURSED
            ]); //
            $table->date("disbursement_date")->nullable(); //
            $table->float("amount_disbursed")->nullable(); //
            $table->enum("application_status", [
                LoanApplicationStatus::APPROVED_BY_BRANCH_MANAGER(),
                LoanApplicationStatus::DISAPPROVED_BY_BRANCH_MANAGER(),
                LoanApplicationStatus::DISAPPROVED_BY_GLOBAL_MANAGER(),
                LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
                LoanApplicationStatus::PENDING()
            ]);
            $table->enum("loan_condition_status", [
                LoanConditionStatus::ACTIVE,
                LoanConditionStatus::INACTIVE,
                LoanConditionStatus::COMPLETED,
                LoanConditionStatus::NONPERFORMING
            ]);
            $table->enum("loan_default_status", [
               LoanDefaultStatus::DEFAULTING,
                LoanDefaultStatus::NOT_DEFAULTING
            ]); //
            $table->integer("num_of_default_days")
                ->nullable()
                ->comment("Number of days the loan has been defaulted on"); //
            $table->timestamps();

            $table->foreign('user_id')
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
        Schema::dropIfExists('loans');
    }
}
