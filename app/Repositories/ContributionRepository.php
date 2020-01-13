<?php
/**
 * Created by PhpStorm.
 * User: abraham
 * Date: 13/01/2020
 * Time: 6:38 PM
 */

namespace App\Repositories;


use App\GraphQL\Errors\GraphqlError;
use App\Models\MemberContribution;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContributionRepository implements ContributionRepositoryInterface
{

    /**
     * Insert a contribution in the database.
     *
     * @param array $contributionData
     * @return MemberContribution
     */
    public function create(array $contributionData): MemberContribution
    {
        return MemberContribution::create($contributionData);
    }

    /**
     * Update a contribution in the database.
     *
     * @param string $id
     * @param array $contributionData
     * @return MemberContribution
     */
    public function update(string $id, array $contributionData): MemberContribution
    {
        $contribution = $this->find($id);

        $contribution->update($contributionData);
        return $contribution;
    }

    /**
     * Find a contribution by id.
     *
     * @param string $contribution_id
     * @return MemberContribution|null
     */
    public function find(string $contribution_id): MemberContribution
    {
        $contribution = MemberContribution::findOrFail($contribution_id);
        return $contribution;
    }
}
