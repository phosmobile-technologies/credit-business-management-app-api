<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyBranch;
use Faker\Generator as Faker;

$factory->define(CompanyBranch::class, function (Faker $faker) {
    return [
        'company_balance' => $faker->randomFloat(2, 10000, 5000000),
        'name' => $faker->country,
        'location' => $faker->address
    ];
});

$factory->state(CompanyBranch::class, 'with_default_values', function ($faker) {

    return [
        'company_balance' =>null,
    ];
});