<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\UserPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any transactions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the transaction.
     *
     * @param  \App\User $user
     * @param Transaction $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine whether the user can create transactions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the transaction.
     *
     * @param  \App\User $user
     * @param Transaction $transaction
     * @return mixed
     */
    public function update(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine whether the user can delete the transaction.
     *
     * @param  \App\User $user
     * @param Transaction $transaction
     * @return mixed
     */
    public function delete(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine whether the user can restore the transaction.
     *
     * @param  \App\User $user
     * @param Transaction $transaction
     * @return mixed
     */
    public function restore(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the transaction.
     *
     * @param  \App\User $user
     * @param Transaction $transaction
     * @return mixed
     */
    public function forceDelete(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine if the user can process a transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     * @return bool
     */
    public function processTransaction(User $user, Transaction $transaction) {
        return $user->can(UserPermissions::CAN_PROCESS_TRANSACTION);
    }

    /**
     * Determine if the user can initiate a transaction.
     *
     * @param User $user
     * @return bool
     */
    public function initiateTransaction(User $user) {
        return true;
    }
}
