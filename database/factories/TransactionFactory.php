<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\enums\TransactionMedium;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use Faker\Generator as Faker;

$factory->define(\App\Models\Transaction::class, function (Faker $faker) {
    $transactionTypes = [
        TransactionType::BRANCH_EXPENSE,
        TransactionType::BRANCH_FUND_DISBURSEMENT,
        TransactionType::CONTRIBUTION_PAYMENT,
        TransactionType::CONTRIBUTION_WITHDRAWAL,
        TransactionType::DEFAULT_CANCELLATION,
        TransactionType::DEFAULT_REPAYMENT,
        TransactionType::LOAN_DISBURSEMENT,
        TransactionType::LOAN_REPAYMENT,
        TransactionType::VENDOR_PAYOUT
    ];

    $transactionMediums = [
        TransactionMedium::CASH,
        TransactionMedium::ONLINE,
        TransactionMedium::BANK_TRANSFER,
        TransactionMedium::BANK_TELLER
    ];
    $transactionStatustes = [
        TransactionStatus::PENDING,
        TransactionStatus::COMPLETED,
        TransactionStatus::FAILED
    ];

    return [
        'transaction_date' => $faker->dateTime()->format('Y-m-d H:i:s'),
        'transaction_type' => $transactionTypes[array_rand($transactionTypes)],
        'transaction_amount' => $faker->randomFloat(2, 1000, 1000000),
        'transaction_medium' => $transactionMediums[array_rand($transactionMediums)],
        'transaction_purpose' => $faker->realText()
//        'owner_id' => '',
//        'owner_type' => ''
    ];
});


/**
 * Factory state for a transaction that is pending
 */
$factory->state(\App\Models\Transaction::class, 'pending', function ($faker) {
    return [
        'transaction_status' => TransactionStatus::PENDING
    ];
});

/**
 * Factory state for a transaction that is pending
 */
$factory->state(\App\Models\Transaction::class, 'completed', function ($faker) {
    return [
        'transaction_status' => TransactionStatus::COMPLETED
    ];
});

/**
 * Factory state for a transaction that is failed
 */
$factory->state(\App\Models\Transaction::class, 'failed', function ($faker) {
    return [
        'transaction_status' => TransactionStatus::FAILED
    ];
});
