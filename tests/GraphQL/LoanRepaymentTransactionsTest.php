<?php

namespace Tests\GraphQL;

use App\Models\Enums\LoanConditionStatus;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Enums\UserRoles;
use App\Models\Loan;
use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\TransactionsQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanRepaymentTransactionsTest extends TestCase
{

    use RefreshDatabase, InteractsWithTestUsers, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testItInitiatesLoanRepaymentTransactionSuccessfully()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_ACCOUNTANT]);

        $loan = factory(Loan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'loan_balance' => 2000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
        ])->toArray();

        $transactionData = [
            'owner_id' => $loan->id,
            'transaction_details' => $transactionDetails
        ];

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
            'transaction_amount' => $transactionDetails['transaction_amount'],
            'transaction_type' => $transactionDetails['transaction_type'],
            'transaction_purpose' => $transactionDetails['transaction_purpose'],
        ]);
    }

    /**
     * @test
     * @group
     */
    public function testItCorrectlyApprovesALoanRepaymentRequest()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_MANAGER]);

        /**
         * Create a loan
         * Create a loan repayment transaction for the loan (ensure it's pending)
         * Try to approve it, ensure that it's approved
         */

        $loan = factory(Loan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'loan_balance' => 2000,
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id' => $loan->id,
            'owner_type' => TransactionOwnerType::LOAN
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

        $this->assertDatabaseHas(with(new Loan)->getTable(), [
            'id' => $loan->id,
            'loan_balance' => 1500,
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id' => $this->user['id'],
            'transaction_id' => $transaction->id,
            'processing_type' => TransactionProcessingActions::APPROVE,
            'message' => $message
        ]);
    }

    /**
     * @test
     * @group
     */
    public function testItCorrectlyCompletesALoanWhenTheTotalRepaymentIsMade()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_MANAGER]);

        /**
         * Create a loan
         * Create a loan repayment transaction for the loan (ensure it's pending)
         * Try to approve it, ensure that it's approved
         */

        $loan = factory(Loan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'loan_balance' => 2000,
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 2000,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id' => $loan->id,
            'owner_type' => TransactionOwnerType::LOAN
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
                    'transaction_amount' => 2000,
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

        $this->assertDatabaseHas(with(new Loan)->getTable(), [
            'id' => $loan->id,
            'loan_balance' => 0,
            'loan_condition_status' => LoanConditionStatus::COMPLETED
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id' => $this->user['id'],
            'transaction_id' => $transaction->id,
            'processing_type' => TransactionProcessingActions::APPROVE,
            'message' => $message
        ]);
    }

    /**
     * @test
     */
    public function testItCorrectlyDisapprovesALoanRepaymentRequest()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_MANAGER]);

        /**
         * Create a loan
         * Create a loan repayment transaction for the loan (ensure it's pending)
         * Try to approve it, ensure that it's approved
         */

        $loan = factory(Loan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'loan_balance' => 2000,
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id' => $loan->id,
            'owner_type' => TransactionOwnerType::LOAN
        ]);
        $message = $this->faker->text();

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

        $this->assertDatabaseHas(with(new Loan)->getTable(), [
            'id' => $loan->id,
            'loan_balance' => 2000,
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id' => $this->user['id'],
            'transaction_id' => $transaction->id,
            'processing_type' => TransactionProcessingActions::DISAPPROVE,
            'message' => $message
        ]);
    }
}
