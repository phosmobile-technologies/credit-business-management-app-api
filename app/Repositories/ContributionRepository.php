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
use App\Models\Transaction;
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

    /**
     * Add a payment to a contribution plan.
     *
     * @param ContributionPlan $contribution
     * @param Transaction $transaction
     * @return ContributionPlan
     */
    public function addPayment(ContributionPlan $contribution, Transaction $transaction): ContributionPlan
    {
        $contribution->contribution_balance = $contribution->contribution_balance + $transaction->transaction_amount;
        $contribution->save();

        return $contribution;
    }

    /**
     * Withdraw funds from a contribution plan.
     *
     * @param ContributionPlan $contribution
     * @param Transaction $transaction
     * @return ContributionPlan
     * @throws GraphqlError
     */
    public function withdraw(ContributionPlan $contribution, Transaction $transaction): ContributionPlan
    {
        if ($transaction->transaction_amount > $contribution->contribution_balance) {
            throw new GraphqlError("Insufficient Contribution plan balance to make this withdrawal");
        }

        $contribution->contribution_balance = $contribution->contribution_balance - $transaction->transaction_amount;
        $contribution->save();

        return $contribution;
    }
}
