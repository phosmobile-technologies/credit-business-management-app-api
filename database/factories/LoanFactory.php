<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanRepaymentFrequency;
use App\Models\Loan;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    $loanFrequencies = [LoanRepaymentFrequency::WEEKLY, LoanRepaymentFrequency::MONTHLY];
    
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
        'tenure' => $faker->numberBetween(1, 24)
    ];
});

// /**
//  * Factory state for a loan that is defaulting
//  */
// $factory->state(Loan::class, 'defaulting_loan', function ($faker) {
//     return [
//         'loan_default_status' => LoanDefaultStatus::DEFAULTING,
//         'num_of_default_days' => $faker->numberBetween(1, 30)
//     ];
// });

// /**
//  * Factory state for a loan that is not defaulting
//  */
// $factory->state(Loan::class, 'non_defaulting_loan', function ($faker) {
//     return [
//         'loan_default_status' => LoanDefaultStatus::NOT_DEFAULTING,
//         'num_of_default_days' => null
//     ];
// });

// /**
//  * Factory state for a loan that has been disbursed
//  */
// $factory->state(Loan::class, 'disbursed_loan', function ($faker) {
//     return [
//         'disbursement_status' => DisbursementStatus::DISBURSED
//     ];
// });

// /**
//  * Factory state for a loan that has not been disbursed
//  */
// $factory->state(Loan::class, 'not_disbursed_loan', function ($faker) {
//     return [
//         'disbursement_status' => DisbursementStatus::NOT_DISBURSED,
//         'amount_disbursed' => null
//     ];
// });
