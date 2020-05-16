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
use App\Models\enums\ContributionType;
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
     * @param array  $contributionData
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
     * @param Transaction      $transaction
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
            $contribution->status          = ContributionPlan::STATUS_ACTIVE;
            $contribution->activation_date = now();
        }

        $contribution->save();
        return $contribution;
    }

    /**
     * Withdraw from a contribution plan.
     *
     * @param ContributionPlan $contribution
     * @param Transaction      $transaction
     * @return array
     * @throws GraphqlError
     */
    public function withdraw(ContributionPlan $contribution, Transaction $transaction): array
    {
        if (($contribution->contributionStatus === ContributionPlan::STATUS_COMPLETED) && ($contribution->balance > $transaction->transaction_amount)) {
            throw new GraphqlError("Cannot partially withdraw from a completed plan. Redeem all funds from the completed plan instead");
        }

        if ($contribution->contributionStatus === ContributionPlan::STATUS_INACTIVE) {
            throw new GraphqlError("Cannot withdraw from an inactive plan.");
        }

        $amountSentToWallet = $transaction->transaction_amount;
        switch ($contribution->type) {
            case ContributionType::LOCKED:
                throw new GraphqlError("Cannot withdraw from an locked plan.");
                break;

            case ContributionType::FIXED:
                if (!$contribution->isCompleted) {
                    $amountSentToWallet = (0.95 * $transaction->transaction_amount);
                }
                break;

            case ContributionType::GOAL:
                if (!$contribution->isCompleted) {
                    $amountSentToWallet = (0.98 * $transaction->transaction_amount);
                }
                break;
        }

        $contribution->balance = $contribution->balance - $transaction->transaction_amount;
        $contribution->save();

        return [$contribution, $amountSentToWallet];
    }
}
