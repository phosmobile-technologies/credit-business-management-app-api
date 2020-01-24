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
     * Determine whether the user can view any contribution plans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the contribution plan.
     *
     * @param  \App\User $user
     * @param ContributionPlan $contributionPlan
     * @return mixed
     */
    public function view(User $user, ContributionPlan $contributionPlan)
    {
        //
    }

    /**
     * Determine whether the user can create contribution plans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can(UserPermissions::CAN_CREATE_CONTRIBUTION);
    }

    /**
     * Determine whether the user can update the contribution plan.
     *
     * @param  \App\User $user
     * @param ContributionPlan $contributionPlan
     * @return mixed
     */
    public function update(User $user, ContributionPlan $contributionPlan)
    {
        return $user->can(UserPermissions::CAN_UPDATE_CONTRIBUTION);
    }

    /**
     * Determine whether the user can delete the contribution plan.
     *
     * @param  \App\User $user
     * @param ContributionPlan $contributionPlan
     * @return mixed
     */
    public function delete(User $user, ContributionPlan $contributionPlan)
    {
        return $user->can(UserPermissions::CAN_DELETE_CONTRIBUTION);
    }

    /**
     * Determine whether the user can restore the contribution plan.
     *
     * @param  \App\User $user
     * @param ContributionPlan $contributionPlan
     * @return mixed
     */
    public function restore(User $user, ContributionPlan $contributionPlan)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the contribution plan.
     *
     * @param  \App\User $user
     * @param ContributionPlan $contributionPlan
     * @return mixed
     */
    public function forceDelete(User $user, ContributionPlan $contributionPlan)
    {
        //
    }

    /**
     * Determine whether the user can initiate a new contribution plan transaction
     *
     * @param  \App\User $user
     * @param ContributionPlan $contributionPlan
     * @return mixed
     */
    public function initiateContributionPlanTransaction(User $user, ContributionPlan $contributionPlan)
    {
        return true;
    }
}
