<?php

namespace App\Policies;

use App\Models\Loan;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any loans.
     *
     * @param  \App\User  $user
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
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
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
}
