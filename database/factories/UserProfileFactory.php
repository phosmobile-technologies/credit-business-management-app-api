<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserProfile;
use Faker\Generator as Faker;

$factory->define(UserProfile::class, function (Faker $faker) {
    $genders = ['MALE', 'FEMALE'];
    $maritalStatus = ['SINGLE', 'MARRIED', 'DIVORCED'];
    $stateOfOrigin = ['Lagos', 'Ogun', 'Delta'];
    $nokRelationship = ['father', 'mother', 'sibling'];

    return [
        'gender' => $genders[array_rand($genders)],
        'date_of_birth' => $faker->date(),
        'marital_status' => $maritalStatus[array_rand($maritalStatus)],
        'occupation' => $faker->text,
        'address' => $faker->address,
        'state_of_origin' => $stateOfOrigin[array_rand($stateOfOrigin)],
        'next_of_kin' => $faker->name,
        'relationship_with_next_of_kin' => $nokRelationship[array_rand($nokRelationship)],
        'account_administrator' => $faker->name,
        'account_name' => $faker->text,
        'account_number' => $faker->numberBetween(100000, 20000000),
        'status' => 'INACTIVE',
        'customer_identifier' => $faker->uuid,
        'company_id' => $faker->uuid,
        'branch_id' => $faker->uuid
    ];
});

/**
 * Factory state for a user profile that includes all default values that cannot be passed when creating a user profile
 */
$factory->state(UserProfile::class, 'with_default_values', function ($faker) {

    return [
        'bvn' => $faker->numberBetween(100000, 20000000),
        'bank_account_number' => $faker->numberBetween(100000, 20000000),
        'bank_account_name' => $faker->name,
        'bank_name' => $faker->name,
    ];
});
