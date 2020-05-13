<?php

namespace Tests\GraphQL\Mutations;

use App\Events\TransactionProcessedEvent;
use App\Models\ContributionPlan;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Enums\UserRoles;
use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\GraphQL\Helpers\Schema\TransactionsQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestContributionPlans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class ContributionPlanPaymentTransactionsTest extends TestCase
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
    public function testItInitiatesContributionPlanPaymentTransactionSuccessfully()
    {
        $contributionData = $this->createContributionPlanAndTransactionData(TransactionType::CONTRIBUTION_PAYMENT);
        $contributionPlan = $contributionData['contributionPlan'];
        $transactionDetails = $contributionData['transactionDetails'];
        $transactionData = $contributionData['transactionData'];
        $response = $this->postGraphQL([
            'query' => TransactionsQueriesAndMutations::initiateTransaction(),
            'variables' => [
                'input' => $transactionData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'InitiateTransaction' => [
                    'transaction_amount' => 500,
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Transaction)->getTable(), [
            'owner_type' => TransactionOwnerType::CONTRIBUTION_PLAN,
            'owner_id' => $contributionPlan->id,
            'transaction_amount' => $transactionDetails['transaction_amount'],
            'transaction_type' => $transactionDetails['transaction_type'],
            'transaction_purpose' => $transactionDetails['transaction_purpose'],
        ]);
    }

    /**
     * @test
     * @group active
     */
    public function testItCorrectlyApprovesAContributionPaymentTransaction()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_ACCOUNTANT]);
        Event::fake([
            TransactionProcessedEvent::class
        ]);

        /**
         * Create a contribution plan
         * Create a contribution plan transaction for the loan (ensure it's pending)
         * Try to approve it, ensure that it's approved
         */

        $contributionPlan = factory(ContributionPlan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 2000,
            'balance' => 1000,
            'status' => ContributionPlan::STATUS_INACTIVE
        ]);

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::CONTRIBUTION_PAYMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id' => $contributionPlan->id,
            'owner_type' => TransactionOwnerType::CONTRIBUTION_PLAN
        ]);
        $message = $this->faker->realText();

        $response = $this->postGraphQL([
            'query' => TransactionsQueriesAndMutations::processTransaction(),
            'variables' => [
                'transaction_id' => $transaction->id,
                'action' => TransactionProcessingActions::APPROVE,
                'message' => $message,
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'ProcessTransaction' => [
                    'id' => $transaction->id,
                    'transaction_amount' => 500,
                    'transaction_status' => TransactionStatus::COMPLETED
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Transaction)->getTable(), [
            'id' => $transaction->id,
            'transaction_status' => TransactionStatus::COMPLETED,
            'transaction_amount' => $transaction->transaction_amount,
            'transaction_type' => $transaction->transaction_type,
            'transaction_purpose' => $transaction->transaction_purpose,
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id' => $this->user['id'],
            'transaction_id' => $transaction->id,
            'processing_type' => TransactionProcessingActions::APPROVE,
            'message' => $message
        ]);

        Event::assertDispatched(TransactionProcessedEvent::class);
    }

    /**
     * @test
     * @group active
     */
    public function testItCorrectlyDisapprovesAContributionPlanPaymentTransaction()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_ACCOUNTANT]);
        Event::fake([
            TransactionProcessedEvent::class
        ]);

        /**
         * Create a contribution plan
         * Create a contribution plan transaction for the loan (ensure it's pending)
         * Try to approve it, ensure that it's approved
         */

        $contributionPlan = factory(ContributionPlan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 2000,
            'balance' => 1000,
        ]);

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::CONTRIBUTION_PAYMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id' => $contributionPlan->id,
            'owner_type' => TransactionOwnerType::CONTRIBUTION_PLAN
        ]);
        $message = $this->faker->realText();

        $response = $this->postGraphQL([
            'query' => TransactionsQueriesAndMutations::processTransaction(),
            'variables' => [
                'transaction_id' => $transaction->id,
                'action' => TransactionProcessingActions::DISAPPROVE,
                'message' => $message,
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'ProcessTransaction' => [
                    'id' => $transaction->id,
                    'transaction_amount' => 500,
                    'transaction_status' => TransactionStatus::FAILED
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Transaction)->getTable(), [
            'id' => $transaction->id,
            'transaction_status' => TransactionStatus::FAILED,
            'transaction_amount' => $transaction->transaction_amount,
            'transaction_type' => $transaction->transaction_type,
            'transaction_purpose' => $transaction->transaction_purpose,
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id' => $this->user['id'],
            'transaction_id' => $transaction->id,
            'processing_type' => TransactionProcessingActions::DISAPPROVE,
            'message' => $message
        ]);

        Event::assertDispatched(TransactionProcessedEvent::class);
    }
}
