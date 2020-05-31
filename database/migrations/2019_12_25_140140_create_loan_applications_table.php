<?php

use App\Models\Enums\LoanRepaymentFrequency;
use App\Models\enums\OnlineLoanApplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->string('loan_purpose');
            $table->string('loan_repayment_source');
            $table->float('loan_amount');
            $table->enum('loan_repayment_frequency', [
                LoanRepaymentFrequency::MONTHLY,
                LoanRepaymentFrequency::WEEKLY
            ]);
            $table->enum('status', [
                OnlineLoanApplicationStatus::PENDING,
                OnlineLoanApplicationStatus::DISAPPROVED,
                OnlineLoanApplicationStatus::APPROVED
            ])->default(OnlineLoanApplicationStatus::PENDING);
            $table->integer('tenure');
            $table->date('expected_disbursement_date');
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
        Schema::dropIfExists('loan_applications');
    }
}
