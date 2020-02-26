<?php

namespace App\Policies;

use App\Models\Wallet;
use App\Models\UserPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WalletPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any wallets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the wallet.
     *
     * @param  \App\User $user
     * @param Wallet $wallet
     * @return mixed
     */
    public function view(User $user, Wallet $wallet)
    {
        //
    }

    /**
     * Determine whether the user can create wallets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(UserPermissions::CAN_CREATE_WALLET);
    }

    /**
     * Determine whether the user can update the wallet.
     *
     * @param  \App\User $user
     * @param Wallet $wallet
     * @return mixed
     */
    public function update(User $user, Wallet $wallet)
    {
        return $user->can(UserPermissions::CAN_UPDATE_WALLET);
    }

    /**
     * Determine whether the user can delete the wallet.
     *
     * @param  \App\User $user
     * @param Wallet $wallet
     * @return mixed
     */
    public function delete(User $user, Wallet $wallet)
    {
        //
    }

    /**
     * Determine whether the user can restore the wallet.
     *
     * @param  \App\User $user
     * @param Wallet $wallet
     * @return mixed
     */
    public function restore(User $user, Wallet $wallet)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the wallet.
     *
     * @param  \App\User $user
     * @param Wallet $wallet
     * @return mixed
     */
    public function forceDelete(User $user, Wallet $wallet)
    {
        //
    }

    /**
     * Determine whether the user can initiate a new wallet transaction
     *
     * @param  \App\User $user
     * @param Wallet $wallet
     * @return mixed
     */
    public function initiateWalletTransaction(User $user, Wallet $wallet)
    {
        return true;
    }
}
