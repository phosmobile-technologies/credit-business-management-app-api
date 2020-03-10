<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Enums\LoanRepaymentFrequency;
use App\Models\LoanApplication;
use Faker\Generator as Faker;

$factory->define(LoanApplication::class, function (Faker $faker) {
    $loanFrequencies = [LoanRepaymentFrequency::WEEKLY, LoanRepaymentFrequency::MONTHLY];

    return [
        "loan_purpose" => $faker->realText(100),
        "loan_repayment_source" => $faker->realText(100),
        "loan_amount" => $faker->randomFloat(2, 10000, 10000000),
        "loan_repayment_frequency" => $loanFrequencies[array_rand($loanFrequencies)],
        "tenure" => $faker->numberBetween(1, 24),
        "expected_disbursement_date" => $faker->date(),
    ];
});
