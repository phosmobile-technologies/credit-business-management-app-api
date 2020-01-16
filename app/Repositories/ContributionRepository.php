<?php
/**
 * Created by PhpStorm.
 * User: abraham
 * Date: 13/01/2020
 * Time: 6:38 PM
 */

namespace App\Repositories;


use App\GraphQL\Errors\GraphqlError;
use App\Models\ContributionPlan;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContributionRepository implements ContributionRepositoryInterface
{

    /**
     * Insert a contribution in the database.
     *
     * @param array $contributionData
     * @return ContributionPlan
     */
    public function create(array $contributionData): ContributionPlan
    {
        return ContributionPlan::create($contributionData);
    }

    /**
     * Update a contribution in the database.
     *
     * @param string $id
     * @param array $contributionData
     * @return ContributionPlan
     */
    public function update(string $id, array $contributionData): ContributionPlan
    {
        $contribution = $this->find($id);

        $contribution->update($contributionData);
        return $contribution;
    }

    /**
     * Find a contribution by id.
     *
     * @param string $contribution_id
     * @return ContributionPlan|null
     */
    public function find(string $contribution_id): ContributionPlan
    {
        $contribution = ContributionPlan::findOrFail($contribution_id);
        return $contribution;
    }
}
