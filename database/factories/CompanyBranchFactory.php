<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyBranch;
use Faker\Generator as Faker;

$factory->define(CompanyBranch::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
        'location' => $faker->address
    ];
});
