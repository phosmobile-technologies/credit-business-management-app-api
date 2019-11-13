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
}
