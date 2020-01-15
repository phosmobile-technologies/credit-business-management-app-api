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
        'transaction_date' => $faker->date(),
        'transaction_type' => $transactionTypes[array_rand($transactionTypes)],
        'transaction_amount' => $faker->randomFloat(2, 1000, 1000000),
        'transaction_medium' => $transactionMediums[array_rand($transactionMediums)],
        'transaction_purpose' => $faker->realText(),
        'transaction_status' => $transactionStatustes[array_rand($transactionStatustes)],
        'owner_id' => '',
        'owner_type' => ''
    ];
});
