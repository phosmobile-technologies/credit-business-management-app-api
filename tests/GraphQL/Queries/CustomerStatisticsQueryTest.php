<?php

namespace Tests\GraphQL\Queries;

use App\Models\ContributionPlan;
use App\Models\enums\ContributionType;
use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\Loan;
use App\Models\Wallet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\GraphQL\Helpers\Schema\CustomerQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestContributionPlans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class CustomerStatisticsQueryTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestContributionPlans, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testItCorrectlyGetClientLoanStatistics()
    {
        /**
         * Create a customer with an active loan
         * Run the graphql query
         * Assert that the correct data is returned
         */

        $this->loginTestUserAndGetAuthHeaders();

        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER,
            'user_id' => $this->user['id'],
            "loan_amount" => 1000,
            'loan_balance' => 500,
            'disbursement_status' => DisbursementStatus::DISBURSED,
            'disbursement_date' => Carbon::today(),
            'amount_disbursed' => 1000,
            'loan_condition_status' => LoanConditionStatus::ACTIVE,
            'loan_default_status' => LoanDefaultStatus::NOT_DEFAULTING
        ]);

        $goalContributionPlans = factory(ContributionPlan::class, 3)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 2000,
            'balance' => 1000,
            'type' => ContributionType::GOAL
        ]);

        $lockedContributionPlans = factory(ContributionPlan::class, 3)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 2000,
            'balance' => 1000,
            'type' => ContributionType::LOCKED
        ]);

        $fixedContributionPlans = factory(ContributionPlan::class, 3)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 2000,
            'balance' => 1000,
            'type' => ContributionType::FIXED
        ]);

        $wallet = factory(Wallet::class)->create([
            'wallet_balance' => 1000,
            'user_id' => $this->user['id']
        ]);

        $user = User::find($this->user['id']);

        $response = $this->postGraphQL([
            'query' => CustomerQueriesAndMutations::GetCustomerStatisticsQuery(),
            'variables' => [
                'customer_id' => $this->user['id']
            ],
        ], $this->headers);

        // @TODO Change the 'next_due_payment' and 'next_repayment_date' to actually calculate it.
        $response->assertJson([
            'data' => [
                'GetCustomerStatistics' => [
                    'loan_statistics' => [
                        'loan_balance' => $loan->loan_balance,
                        'next_due_payment' => 0,
                        'next_repayment_date' => "N/A",
                        'default_charges' => $loan->num_of_default_days * $loan->default_amount,
                        'total_paid_amount' => $loan->amount_disbursed + $loan->totalInterestAmount - $loan->loan_balance,
                        'active_loan' => true,
                    ],

                    'contribution_plan_statistics' => [
                        'total_contribution_sum' => 18000.00,
                        'goal_contribution_sum' => 6000.00,
                        'fixed_contribution_sum' => 6000.00,
                        'locked_contribution_sum' => 6000.00,
                        'wallet_balance' => $user->wallet->wallet_balance,
                    ]
                ]
            ]
        ]);
    }

}
