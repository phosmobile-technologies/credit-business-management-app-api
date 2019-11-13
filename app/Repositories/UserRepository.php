<?php

namespace App\Repositories;


use App\Events\NewUserRegistered;
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

        event(new NewUserRegistered($user));

        return $user;
    }
}
