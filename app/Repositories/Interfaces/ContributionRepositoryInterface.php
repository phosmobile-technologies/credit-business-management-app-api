<?php

namespace App\Repositories\Interfaces;


use App\Models\MemberContribution;

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
     * @return MemberContribution
     */
    public function create(array $contributionData): MemberContribution;

    /**
     * Update a contribution in the database.
     *
     * @param string $id
     * @param array $contributionData
     * @return MemberContribution
     */
    public function update(string $id ,array $contributionData): MemberContribution;

    /**
     * Find a contribution by id.
     *
     * @param string $contribution_id
     * @return MemberContribution|null
     */
    public function find(string $contribution_id): ?MemberContribution;
}
