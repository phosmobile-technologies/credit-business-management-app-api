<?php

use App\Models\Enums\RequestStatus;
use App\Models\Enums\RequestType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerWithdrawalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_withdrawal_requests', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->float('request_amount');
            $table->enum("request_status", [
                RequestStatus::APPROVED_BY_BRANCH_MANAGER(),
                RequestStatus::DISAPPROVED_BY_BRANCH_MANAGER(),
                RequestStatus::DISAPPROVED_BY_GLOBAL_MANAGER(),
                RequestStatus::APPROVED_BY_GLOBAL_MANAGER(),
                RequestStatus::PENDING(),
                RequestStatus::DISBURSED
            ])->default(RequestStatus::PENDING);
            $table->enum("request_type", [
                RequestType::BRANCH_FUND,
                RequestType::BRANCH_EXTRA_FUND,
                RequestType::DEFAULT_CANCELLATION,
                RequestType::VENDOR_PAYOUT,
                RequestType::CONTRIBUTION_WITHDRAWAL
            ]);
            $table->date('request_date')->nullable();
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
        Schema::dropIfExists('customer_withdrawal_requests');
    }
}
