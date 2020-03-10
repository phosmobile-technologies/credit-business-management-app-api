<?php

namespace Tests\GraphQL\Helpers\Traits;


use App\Models\ContributionPlan;
use App\Models\enums\TransactionType;
use App\Models\Transaction;

trait InteractsWithTestContributionPlans
{
    use InteractsWithTestUsers;

    /**
     * Create a test contribution plan.
     *
     * @param string $transactionType
     * @param array|null $userRoles
     * @return array
     */
    public function createContributionPlanAndTransactionData(string $transactionType, array $userRoles = null)
    {
        if ($userRoles) {
            $this->loginTestUserAndGetAuthHeaders($userRoles);
        } else {
            $this->loginTestUserAndGetAuthHeaders();
        }

        $contributionPlan = factory(ContributionPlan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'contribution_amount' => 2000,
            'contribution_balance' => 1000,
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'transaction_amount' => 500,
            'transaction_type' => $transactionType,
        ])->toArray();

        $transactionData = [
            'owner_id' => $contributionPlan->id,
            'transaction_details' => $transactionDetails
        ];

        return [
            'contributionPlan' => $contributionPlan,
            'transactionDetails' => $transactionDetails,
            'transactionData' => $transactionData,
        ];
    }
}
