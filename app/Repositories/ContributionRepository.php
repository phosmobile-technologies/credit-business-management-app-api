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
use Carbon\Carbon;
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
     * @throws GraphqlError
     */
    public function addPayment(ContributionPlan $contribution, Transaction $transaction): ContributionPlan
    {
        $contribution->balance = $contribution->balance + $transaction->transaction_amount;

        if ($contribution->status === ContributionPlan::STATUS_COMPLETED) {
            throw new GraphqlError("The contribution plan is already completed, and can no longer be funded");
        }

        // This is the first payment made to the contribution plan
        if ($contribution->status === ContributionPlan::STATUS_INACTIVE && !isset($contribution->activation_date)) {
            $contribution->status = ContributionPlan::STATUS_ACTIVE;
            $contribution->activation_date = Carbon::today();
        }

        $contribution->save();
        return $contribution;
    }

    /**
     * Withdraw from a contribution plan.
     *
     * @param ContributionPlan $contribution
     * @param Transaction $transaction
     * @return ContributionPlan
     */
    public function withdraw(ContributionPlan $contribution, Transaction $transaction): ContributionPlan
    {
        $contribution->balance = $contribution->balance - $transaction->transaction_amount;
        $contribution->save();

        return $contribution;
    }
}
