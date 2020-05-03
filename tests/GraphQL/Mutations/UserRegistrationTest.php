<?php

namespace Tests\GraphQL\Mutations;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\enums\RegistrationSource;
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Models\Enums\UserRoles;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function testItSuccessfullyRegistersANewUser()
    {
        $this->seed('TestDatabaseSeeder');

        $userData = collect(factory(User::class)->make())->except(['email_verified_at'])->toArray();

        $company = Company::first();
        $branch = CompanyBranch::inRandomOrder()->first();
        $userProfileData = factory(UserProfile::class)->make([
            'company_id' => $company->id,
            'branch_id' => $branch->id
        ]);
        $userProfileData = collect($userProfileData)->except(['customer_identifier'])->toArray();
        $userProfileData['roles'] = [UserRoles::CUSTOMER];

        $registrationData = array_merge($userData, $userProfileData);
        $registrationData['registration_source'] = RegistrationSource::ONLINE;
        $registrationData['password'] = 'password';
        $registrationData['password_confirmation'] = 'password';

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->postGraphQL([
            'query' => '
            mutation register($input: RegisterInput) {
                register(input: $input) {
                    user {
                        first_name,
                        last_name,
                        email,
                        phone_number,
                        profile {
                            occupation,
                            address,
                            state_of_origin,
                            bvn,
                            bank_account_number
                        }  
                    }
                }
            }
        ',
            'variables' => [
                'input' => $registrationData
            ],
        ]);

        $response->assertJson([
            'data' => [
                'register' => [
                    'user' => [
                        'first_name' => $userData['first_name'],
                        'last_name' => $userData['last_name'],
                        'email' => $userData['email'],
                        'phone_number' => $userData['phone_number'],
                        'profile' => [
                            'occupation' => $userProfileData['occupation'],
                            'address' => $userProfileData['address'],
                            'state_of_origin' => $userProfileData['state_of_origin'],
                            'bvn' => $userProfileData['bvn'],
                            'bank_account_number' => $userProfileData['bank_account_number']
                        ]
                    ]
                ]
            ]
        ]);
    }

}
