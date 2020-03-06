<?php

namespace App\Repositories\Interfaces;

use App\Models\UserProfile;;


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
     * Insert a UserProfile in the database
     *
     * @param array $userProfileData
     * @return UserProfile
     */
    public function create(array $userProfileData): UserProfile;
}
