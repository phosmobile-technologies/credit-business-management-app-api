<?php

namespace Tests\GraphQL\Mutations;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Enums\UserRoles;
use App\Models\UserProfile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\AuthenticationQueriesAndMutations;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_login() {
        $this->seed('TestDatabaseSeeder');
        $this->setUpPassportClient();

        $userData = collect(factory(User::class)->create([
            'password' => 'password'
        ]))->toArray();

        $company = Company::first();
        $branch = CompanyBranch::inRandomOrder()->first();
        $userProfileData = factory(UserProfile::class)->make([
            'company_id' => $company->id,
            'branch_id' => $branch->id
        ]);
        $userProfileData = collect($userProfileData)->except(['customer_identifier'])->toArray();
        $userProfileData['roles'] = [UserRoles::CUSTOMER];

        $input = [
            'username' => $userData['email'],
            'password' => 'password'
        ];

        $response = $this->postGraphQL([
            'query' => AuthenticationQueriesAndMutations::login(),
            'variables' => [
                'input' => $input
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
               'data' => ['login' => ['access_token']]
            ]);
    }
}
