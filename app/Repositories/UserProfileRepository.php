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
     * Find a User profile by for a user.
     *
     * @param string $user_id
     * @return UserProfile|null
     */
    public function findByUserId(string $user_id): ?UserProfile
    {
        return UserProfile::where('user_id', $user_id)->firstOrFail();
    }
}
