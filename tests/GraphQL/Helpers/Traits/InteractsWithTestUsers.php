<?php

namespace Tests\GraphQL\Helpers\Traits;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Enums\UserRoles;
use App\Models\UserProfile;
use App\User;

/**
 * Trait InteractsWithTestUsers
 *
 * This trait houses various user related actions that help with testing.
 *
 * @package Tests\GraphQL\Helpers\Traits
 */
trait InteractsWithTestUsers
{
    use InteractsWithGraphQLRequests;

    /**
     * @var
     */
    public $user;

    /**
     * @var array
     */
    public $headers;

    /**
     * Create a new user.
     *
     * @return User
     */
    public function createUser(): User {
        $company = Company::first();
        $branch = CompanyBranch::first();

        $user = factory(User::class)->create();
        $user->profile()->save(
            factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ])
        );
        $user->user_profile_id = $user->profile->id;
        $user->save();

        return $user;
    }

    /**
     * Create a test user, login, and get authentication headers
     * @param array $userRoles
     */
    public function loginTestUserAndGetAuthHeaders(array $userRoles = [UserRoles::CUSTOMER]) {
        $testUserDetails = $this->createLoginAndGetTestUserDetails($userRoles);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);
    }
}
