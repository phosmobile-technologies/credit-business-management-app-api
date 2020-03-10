<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\UserPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any loans.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the loan.
     *
     * @param  \App\User $user
     * @param Loan $loan
     * @return mixed
     */
    public function view(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can create loans.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(UserPermissions::CAN_CREATE_LOANS);
    }

    /**
     * Determine whether the user can update the loan.
     *
     * @param  \App\User $user
     * @param Loan $loan
     * @return mixed
     */
    public function update(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can delete the loan.
     *
     * @param  \App\User $user
     * @param Loan $loan
     * @return mixed
     */
    public function delete(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can restore the loan.
     *
     * @param  \App\User $user
     * @param Loan $loan
     * @return mixed
     */
    public function restore(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the loan.
     *
     * @param  \App\User $user
     * @param Loan $loan
     * @return mixed
     */
    public function forceDelete(User $user, Loan $loan)
    {
        //
    }

    /**
     * Determine whether the user can update the loan status
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function updateStatus(User $user)
    {
        return $user->can(UserPermissions::CAN_UPDATE_LOAN_STATUS);
    }

    /**
     * Determine whether the user can disburse a loan
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function disburse(User $user)
    {
        return $user->can(UserPermissions::CAN_DISBURSE_LOAN);
    }

    /**
     * Determine whether the user can initiate a Loan repayment.
     *
     * @param User $user
     * @param Loan $loan
     * @return bool
     */
    public function initiateRepayment(User $user, Loan $loan)
    {
        return $user->can(UserPermissions::CAN_INITIATE_LOAN_REPAYMENT)
            || ($loan->user_id === $user->id);
    }

    /**
     * Determine whether the user can process a Loan repayment.
     *
     * @param User $user
     * @param Loan $loan
     * @return bool
     */
    public function processRepayment(User $user, Loan $loan)
    {
        return $user->can(UserPermissions::CAN_INITIATE_LOAN_REPAYMENT)
            || ($loan->user_id === $user->id);
    }
}
