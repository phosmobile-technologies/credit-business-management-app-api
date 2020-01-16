<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\enums\ContributionFrequency;
use App\Models\enums\ContributionType;
use App\Models\ContributionPlan;
use Faker\Generator as Faker;

$factory->define(ContributionPlan::class, function (Faker $faker) {
    $contributionTypes = [
        ContributionType::FIXED,
        ContributionType::WALLET,
        ContributionType::LOCKED,
        ContributionType::GOAL
    ];

    $contributionFrequencies = [
      ContributionFrequency::DAILY,
      ContributionFrequency::WEEKLY,
      ContributionFrequency::MONTHLY,
      ContributionFrequency::QUARTERLY
    ];

    return [
        'contribution_type' => $contributionTypes[array_rand($contributionTypes)],
        'contribution_amount' => $faker->randomFloat(2, 10000, 10000000),
        'contribution_name' => $faker->words(3, true),
        'contribution_duration' => $faker->numberBetween(1, 24),
        'contribution_balance' => $faker->randomFloat(2, 10000, 5000000),
        'contribution_interest_rate' => $faker->randomFloat(2, 1, 50),
        'contribution_frequency' => $contributionFrequencies[array_rand($contributionFrequencies)],
    ];
});
