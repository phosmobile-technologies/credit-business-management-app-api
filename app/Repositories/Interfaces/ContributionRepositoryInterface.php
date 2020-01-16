<?php

namespace App\Repositories\Interfaces;


use App\Models\ContributionPlan;

/**
 * Interface ContributionRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface ContributionRepositoryInterface
{
    /**
     * Create a contribution in the database.
     *
     * @param array $contributionData
     * @return ContributionPlan
     */
    public function create(array $contributionData): ContributionPlan;

    /**
     * Update a contribution in the database.
     *
     * @param string $id
     * @param array $contributionData
     * @return ContributionPlan
     */
    public function update(string $id ,array $contributionData): ContributionPlan;

    /**
     * Find a contribution by id.
     *
     * @param string $contribution_id
     * @return ContributionPlan|null
     */
    public function find(string $contribution_id): ?ContributionPlan;
}
