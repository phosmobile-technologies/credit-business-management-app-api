<?php

namespace App\Policies;

use App\Models\CustomerWithdrawalRequest;
use App\Models\UserPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerWithdrawalRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customer withdrawal requests.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the customer withdrawal request.
     *
     * @param  \App\User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @return mixed
     */
    public function view(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest)
    {
        //
    }

    /**
     * Determine whether the user can create customer withdrawal requests.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(UserPermissions::CAN_CREATE_CUSTOMER_WITHDRAWAL_REQUEST);
    }

    /**
     * Determine whether the user can update the customer withdrawal request.
     *
     * @param  \App\User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @return mixed
     */
    public function update(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest)
    {
        return $user->can(UserPermissions::CAN_UPDATE_CUSTOMER_WITHDRAWAL_REQUEST);
    }

    /**
     * Determine whether the user can delete the customer withdrawal request.
     *
     * @param  \App\User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @return mixed
     */
    public function delete(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest)
    {
        return $user->can(UserPermissions::CAN_DELETE_CUSTOMER_WITHDRAWAL_REQUEST);
    }

    /**
     * Determine whether the user can restore the customer withdrawal request.
     *
     * @param  \App\User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @return mixed
     */
    public function restore(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the customer withdrawal request.
     *
     * @param  \App\User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @return mixed
     */
    public function forceDelete(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest)
    {
        //
    }

    /**
     * Determine whether the user can initiate a new customer withdrawal request transaction
     *
     * @param  \App\User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @return mixed
     */
    public function initiateCustomerWithdrawalRequest(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest)
    {
        return true;
    }
}
