<?php

namespace Tests\GraphQL\Helpers\Traits;
use App\Models\Company;
use App\Models\CompanyBranch;
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
    /**
     * Create a new user.
     *
     * @return User
     */
    public function createUser(): User {
        $company = Company::first();
        $branch = CompanyBranch::inRandomOrder()->first();

        $user = factory(User::class)->create();
        $user->profile()->save(
            factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ])
        );

        return $user;
    }
}
