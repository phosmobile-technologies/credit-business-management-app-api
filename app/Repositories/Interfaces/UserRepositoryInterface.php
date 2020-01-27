<?php

namespace App\Repositories\Interfaces;


use App\User;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    /**
     * Find a User by id.
     *
     * @param string $user_id
     * @return User|null
     */
    public function find(string $user_id): ?User;

    /**
     * Get the eloquent query builder that can get loan transactions that belong to a customer.
     *sss
     * @param string $customer_id
     * @return HasManyThrough
     */
    public function findLoanTransactionsQuery(string $customer_id): HasManyThrough;

    /**
     * Get the eloquent query builder that can get contribution plan transactions that belong to a customer.
     *sss
     * @param string $customer_id
     * @return HasManyThrough
     */
    public function findContributionPlanTransactionsQuery(string $customer_id): HasManyThrough;
}
