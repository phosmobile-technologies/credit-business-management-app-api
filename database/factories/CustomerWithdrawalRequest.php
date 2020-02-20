<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Enums\RequestStatus;
use App\Models\Enums\RequestType;
use App\Models\CustomerWithdrawalRequest;
use Faker\Generator as Faker;

/**
 * Factory state for Request Statuses
 */
$factory->define(CustomerWithdrawalRequest::class, function (Faker $faker) {
    $RequestStatuses = [
        RequestStatus::APPROVED_BY_BRANCH_MANAGER,
        RequestStatus::DISAPPROVED_BY_BRANCH_MANAGER,
        RequestStatus::DISAPPROVED_BY_GLOBAL_MANAGER,
        RequestStatus::APPROVED_BY_GLOBAL_MANAGER,
        RequestStatus::PENDING,
        RequestStatus::DISBURSED
    ];

    /**
     * Factory state for Request Types
     */
    $RequestTypes = [
        RequestType::BRANCH_FUND,
        RequestType::BRANCH_EXTRA_FUND,
        RequestType::DEFAULT_CANCELLATION,
        RequestType::VENDOR_PAYOUT,
        RequestType::CONTRIBUTION_WITHDRAWAL
    ];
    $requestAmount =  $faker->randomFloat(2, 10000, 10000000);
    $amountPaid = $faker->randomFloat(2, 10000, 5000000);

    return [
        'request_amount' => $requestAmount,
        'request_balance' => $requestAmount - $amountPaid,
        'request_status' => $RequestStatuses[array_rand($RequestStatuses)],
        'request_type' => $RequestTypes[array_rand($RequestTypes)],
        'request_date' => $faker->date(),
    ];
});
