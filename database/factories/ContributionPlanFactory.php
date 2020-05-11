<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\enums\ContributionFrequency;
use App\Models\enums\ContributionType;
use App\Models\ContributionPlan;
use Faker\Generator as Faker;

$factory->define(ContributionPlan::class, function (Faker $faker) {
    $contributionTypes = [
        ContributionType::FIXED,
        ContributionType::LOCKED,
        ContributionType::GOAL
    ];

    $contributionFrequencies = [
        ContributionFrequency::DAILY,
        ContributionFrequency::WEEKLY,
        ContributionFrequency::MONTHLY,
        ContributionFrequency::QUARTERLY
    ];

    $contributionStatuses = [
        ContributionPlan::STATUS_ACTIVE,
        ContributionPlan::STATUS_INACTIVE,
        ContributionPlan::STATUS_COMPLETED
    ];

    return [
        'type' => $contributionTypes[array_rand($contributionTypes)],
        'goal' => $faker->randomFloat(2, 10000, 10000000),
        'name' => $faker->words(3, true),
        'duration' => $faker->numberBetween(1, 24),
        'balance' => $faker->randomFloat(2, 10000, 5000000),
        'interest_rate' => $faker->randomFloat(2, 1, 50),
        'frequency' => $contributionFrequencies[array_rand($contributionFrequencies)],
        'payback_date' => Carbon\Carbon::createFromFormat('Y-m-d', $faker->date('Y-m-d'))->toDateString(),
        'start_date' => Carbon\Carbon::createFromFormat('Y-m-d', $faker->date('Y-m-d'))->toDateString(),
        'fixed_amount' => $faker->randomFloat(2, 10000, 10000000),
        'status' => $contributionStatuses[array_rand($contributionStatuses)]
    ];
});

/**
 * Factory state for a contribution plan that includes all default values that cannot be passed when creating a contribution
 */
$factory->state(ContributionPlan::class, 'with_default_values', function ($faker) {
    $contributionTypes = [
        ContributionType::FIXED,
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
        'balance' => $faker->randomFloat(2, 10000, 5000000),
    ];
});
