<?php

namespace App\Repositories\Interfaces;


use App\User;

interface UserRepositoryInterface
{
    /**
     * Create a new user
     *
     * @param array $userData
     * @return User
     */
    public function createUser(array $userData): User;

    /**
     * Attach the user profile data to a user.
     * This will usually be called when a new user is registered.
     *
     * @param User $user
     * @param array $userProfileData
     * @return User
     */
    public function attachUserProfile(User $user, array $userProfileData): User;

    /**
     * Attach the user roles to a user.
     * This will usually be called when a new user is registered.
     *
     * @param User $user
     * @param array $roles
     * @return User
     */
    public function attachUserRoles(User $user, array $roles): User;
}
