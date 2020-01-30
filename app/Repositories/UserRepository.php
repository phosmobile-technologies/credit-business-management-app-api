<?php

namespace App\Repositories;


use App\Models\UserProfile;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    /**
     * Find a User by id.
     *
     * @param string $user_id
     * @return User|null
     */
    public function find(string $user_id): ?User
    {
        return User::findOrFail($user_id);
    }

    /**
     * Get the eloquent query builder that can get transactions that belong to a customer.
     *sss
     * @param string $customer_id
     * @return HasManyThrough
     */
    public function findLoanTransactionsQuery(string $customer_id): HasManyThrough
    {
        $user = $this->find($customer_id);

        return $user->loanTransactions();
    }

    /**
     * Get the eloquent query builder that can get contribution plan transactions that belong to a customer.
     *sss
     * @param string $customer_id
     * @return HasManyThrough
     */
    public function findContributionPlanTransactionsQuery(string $customer_id): HasManyThrough
    {
        $user = $this->find($customer_id);

        return $user->contributionPlansTransactions();
    }
}
