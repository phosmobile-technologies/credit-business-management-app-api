<?php

namespace App\Repositories\Interfaces;


use App\Models\UserProfile;

interface UserProfileRepositoryInterface
{
    /**
     * Determine if a user profile with the profile identifier exists.
     *
     * @param int $identifier
     * @return bool
     */
    public function customerIdentifierExists(int $identifier): bool;

    /**
     * Find a User profile by user id.
     *
     * @param string $user_id
     * @return UserProfile|null
     */
    public function findByUserId(string $user_id): ?UserProfile;
}
