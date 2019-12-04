<?php

namespace App\Repositories\Interfaces;


interface UserProfileRepositoryInterface
{
    /**
     * Determine if a user profile with the profile identifier exists.
     *
     * @param int $identifier
     * @return bool
     */
    public function customerIdentifierExists(int $identifier): bool;
}
