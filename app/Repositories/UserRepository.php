<?php

namespace App\Repositories;


use App\Models\UserProfile;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user
     *
     * @param array $userData
     * @return User
     */
    public function createUser(array $userData): User
    {
        $user = User::create($userData);

        return $user;
    }

    /**
     * Attach the user profile data to a user.
     * This will usually be called when a new user is registered.
     *
     * @param User $user
     * @param array $userProfileData
     * @return User
     */
    public function attachUserProfile(User $user, array $userProfileData): User
    {
        $user->profile()->save(new UserProfile($userProfileData));
        $user->user_profile_id = $user->profile->id;
        $user->save();

        return $user;
    }

    /**
     * Attach the user roles to a user.
     * This will usually be called when a new user is registered.
     *
     * @param User $user
     * @param array $roles
     * @return User
     */
    public function attachUserRoles(User $user, array $roles): User
    {
        $user->assignRole($roles);

        return $user;
    }
}
