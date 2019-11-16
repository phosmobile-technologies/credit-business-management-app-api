<?php

namespace Tests\GraphQL;

use App\Models\UserProfile;
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
        $userData = collect(factory(User::class)->make())->except(['email_verified_at', 'password'])->toArray();
        $userProfileData = factory(UserProfile::class)->make()->toArray();
        $registrationData = array_merge($userData, $userProfileData);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->postGraphQL([
            'query' => '
            mutation register($input: RegisterInput) {
                register(input: $input) {
                    user {
                        first_name,
                        last_name,
                        email,
                        phone_number
                        profile {
                            occupation,
                            address,
                            state_of_origin,
                            saving_amount,
                            frequency_of_saving
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
                            'saving_amount' => $userProfileData['saving_amount'],
                            'frequency_of_saving' => $userProfileData['frequency_of_saving'],
                        ]
                    ]
                ]
            ]
        ]);
    }

}
