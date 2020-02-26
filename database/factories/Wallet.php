<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Wallet;
use Faker\Generator as Faker;

$factory->define(Wallet::class, function (Faker $faker) {
    return [
        'wallet_amount' => $faker->randomFloat(2, 10000, 10000000),
        'wallet_balance' => $faker->randomFloat(2, 10000, 5000000),
    ];
});