<?php

namespace App\Policies;

use App\Models\ContributionPlan;
use App\Models\UserPermissions;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContributionPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any member contributions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the member contribution.
     *
     * @param  \App\User $user
     * @param ContributionPlan $ContributionPlan
     * @return mixed
     */
    public function view(User $user, ContributionPlan $ContributionPlan)
    {
        //
    }

    /**
     * Determine whether the user can create member contributions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(UserPermissions::CAN_CREATE_CONTRIBUTION);
    }

    /**
     * Determine whether the user can update the member contribution.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can(UserPermissions::CAN_UPDATE_CONTRIBUTION);
    }

    /**
     * Determine whether the user can delete the member contribution.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can(UserPermissions::CAN_DELETE_CONTRIBUTION);
    }

    /**
     * Determine whether the user can restore the member contribution.
     *
     * @param  \App\User $user
     * @param ContributionPlan $ContributionPlan
     * @return mixed
     */
    public function restore(User $user, ContributionPlan $ContributionPlan)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the member contribution.
     *
     * @param  \App\User $user
     * @param ContributionPlan $ContributionPlan
     * @return mixed
     */
    public function forceDelete(User $user, ContributionPlan $ContributionPlan)
    {
        //
    }
}
