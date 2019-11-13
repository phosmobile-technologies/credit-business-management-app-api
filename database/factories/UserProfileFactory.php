<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserProfile;
use App\User;
use Faker\Generator as Faker;

$factory->define(UserProfile::class, function (Faker $faker) {
    $genders = ['MALE', 'FEMALE'];
    $maritalStatus = ['SINGLE', 'MARRIED', 'DIVORCED'];
    $stateOfOrigin = ['Lagos', 'Ogun', 'Delta'];
    $frequencyOfSaving = ['WEEKLY', 'MONTHLY', 'QUARTERLY'];
    $nokRelationship = ['father', 'mother', 'sibling'];

    return [
        'gender' => $genders[array_rand($genders)],
        'date_of_birth' => $faker->date(),
        'marital_status' => $maritalStatus[array_rand($maritalStatus)],
        'occupation' => $faker->text,
        'address' => $faker->address,
        'state_of_origin' => $stateOfOrigin[array_rand($stateOfOrigin)],
        'saving_amount' => $faker->numberBetween(1, 20000000),
        'frequency_of_saving' => $frequencyOfSaving[array_rand($frequencyOfSaving)],
        'next_of_kin' => $faker->name,
        'relationship_with_next_of_kin' => $nokRelationship[array_rand($nokRelationship)],
        'account_administrator' => $faker->name,
        'account_name' => $faker->text,
        'account_number' => $faker->numberBetween(100000, 20000000),
        'status' => 'INACTIVE'
    ];
});
