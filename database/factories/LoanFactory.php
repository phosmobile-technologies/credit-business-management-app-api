<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\Enums\LoanRepaymentFrequency;
use App\Models\Loan;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    $loanFrequencies = [LoanRepaymentFrequency::WEEKLY, LoanRepaymentFrequency::MONTHLY];
    $loanConditionStatuses = [
        LoanConditionStatus::NONPERFORMING,
        LoanConditionStatus::COMPLETED,
        LoanConditionStatus::INACTIVE,
        LoanConditionStatus::ACTIVE
    ];
    $loanAmount =  $faker->randomFloat(2, 10000, 10000000);
    $amountPaid = $faker->randomFloat(2, 10000, 5000000);

    return [
        'loan_identifier' => $faker->uuid,
        'loan_purpose' => $faker->realText(),
        'loan_repayment_source' => $faker->realText(),
        'loan_amount' => $loanAmount,
        'interest_rate' => $faker->randomFloat(2, 10, 30),
        'loan_repayment_frequency' => $loanFrequencies[array_rand($loanFrequencies)],
        'service_charge' => $faker->randomFloat(2, 1000, 10000),
        'default_amount' => 1000,
        'tenure' => $faker->numberBetween(1, 24),
    ];
});

/**
 * Factory state for a loan that includes all default values that cannot be passed when creating a loan
 */
$factory->state(Loan::class, 'with_default_values', function ($faker) {
    $loanConditionStatuses = [
        LoanConditionStatus::NONPERFORMING,
        LoanConditionStatus::COMPLETED,
        LoanConditionStatus::INACTIVE,
        LoanConditionStatus::ACTIVE
    ];
    $loanAmount =  $faker->randomFloat(2, 10000, 10000000);
    $amountPaid = $faker->randomFloat(2, 10000, 5000000);

    return [
        'disbursement_status' => DisbursementStatus::DISBURSED,
        'application_status' => LoanApplicationStatus::PENDING,
        'loan_condition_status' => $loanConditionStatuses[array_rand($loanConditionStatuses)],
        'loan_default_status' => LoanDefaultStatus::NOT_DEFAULTING,
        'disbursement_date' => $faker->date(),
        'amount_disbursed' => $amountPaid,
        'num_of_default_days' => null,
        'loan_balance' => $loanAmount -  $amountPaid,
        'next_due_payment' => $faker->date(),
        'due_date' => $faker->date(),
    ];
});

/**
 * Factory state for a loan that is defaulting
 */
$factory->state(Loan::class, 'defaulting_loan', function ($faker) {
    return [
        'loan_default_status' => LoanDefaultStatus::DEFAULTING,
        'num_of_default_days' => $faker->numberBetween(1, 30)
    ];
});

/**
 * Factory state for a loan that is not defaulting
 */
$factory->state(Loan::class, 'non_defaulting_loan', function ($faker) {
    return [
        'loan_default_status' => LoanDefaultStatus::NOT_DEFAULTING,
        'num_of_default_days' => null
    ];
});

/**
 * Factory state for a loan that has been disbursed
 */
$factory->state(Loan::class, 'disbursed_loan', function ($faker) {
    return [
        'disbursement_status' => DisbursementStatus::DISBURSED
    ];
});

/**
 * Factory state for a loan that has not been disbursed
 */
$factory->state(Loan::class, 'not_disbursed_loan', function ($faker) {
    return [
        'disbursement_status' => DisbursementStatus::NOT_DISBURSED,
        'amount_disbursed' => null
    ];
});



