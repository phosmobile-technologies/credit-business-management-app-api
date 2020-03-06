<?php

namespace App\Repositories;


use App\Models\UserProfile;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;

class UserProfileRepository implements UserProfileRepositoryInterface
{

    /**
     * Determine if a user profile with the profile identifier exists.
     *
     * @param int $identifier
     * @return bool
     */
    public function customerIdentifierExists(int $identifier): bool
    {
        return UserProfile::where('customer_identifier', $identifier)->exists();
    }

    /**
     * Insert a UserProfile in the database
     *
     * @param array $userProfileData
     * @return UserProfile
     */
    public function create(array $userProfileData): UserProfile
    {
        return UserProfile::create($userProfileData);
    }

}
