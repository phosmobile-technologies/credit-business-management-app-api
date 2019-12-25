<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\UserPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any loan applications.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the loan application.
     *
     * @param  \App\User $user
     * @param LoanApplication $loanApplication
     * @return mixed
     */
    public function view(User $user, LoanApplication $loanApplication)
    {
        //
    }

    /**
     * Determine whether the user can create loan applications.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(UserPermissions::CAN_CREATE_LOAN_APPLICATIONS);
    }

    /**
     * Determine whether the user can update the loan application.
     *
     * @param  \App\User $user
     * @param LoanApplication $loanApplication
     * @return mixed
     */
    public function update(User $user, LoanApplication $loanApplication)
    {
        //
    }

    /**
     * Determine whether the user can delete the loan application.
     *
     * @param  \App\User $user
     * @param LoanApplication $loanApplication
     * @return mixed
     */
    public function delete(User $user, LoanApplication $loanApplication)
    {
        //
    }

    /**
     * Determine whether the user can restore the loan application.
     *
     * @param  \App\User $user
     * @param LoanApplication $loanApplication
     * @return mixed
     */
    public function restore(User $user, LoanApplication $loanApplication)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the loan application.
     *
     * @param  \App\User $user
     * @param LoanApplication $loanApplication
     * @return mixed
     */
    public function forceDelete(User $user, LoanApplication $loanApplication)
    {
        //
    }
}
