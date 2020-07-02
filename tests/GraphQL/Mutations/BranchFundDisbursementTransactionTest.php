<?php

namespace Tests\GraphQL\Mutations;

use App\Models\CompanyBranch;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Enums\UserRoles;
use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\TransactionsQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestTransactions;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class BranchFundDisbursementTransactionTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testItCorrectlyInitiatesANewBranchFundReimbursementTransaction()
    {
        $this->loginTestUserAndGetAuthHeaders();
        $branch = CompanyBranch::first();

        $transactionData = $this->makeTransaction(TransactionOwnerType::COMPANY_BRANCH, $branch->id, [
            'transaction_type' => TransactionType::BRANCH_FUND_DISBURSEMENT,
            'branch_id'        => $this->user['profile']['branch']['id']
        ]);

        $transactionDetails = $transactionData['transaction_details'];

        $response = $this->postGraphQL([
            'query'     => TransactionsQueriesAndMutations::initiateTransaction(),
            'variables' => [
                'input' => $transactionData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'InitiateTransaction' => [
                    'transaction_amount' => $transactionData['transaction_details']['transaction_amount'],
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Transaction)->getTable(), [
            'owner_type'          => TransactionOwnerType::COMPANY_BRANCH,
            'owner_id'            => $branch->id,
            'transaction_amount'  => $transactionDetails['transaction_amount'],
            'transaction_type'    => $transactionDetails['transaction_type'],
            'transaction_purpose' => $transactionDetails['transaction_purpose'],
        ]);
    }

    /**
     * @test
     */
    public function testItCorrectlyApprovesABranchFundReimbursementTransaction()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_ACCOUNTANT]);

        $branch = CompanyBranch::first();

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 500,
            'transaction_type'   => TransactionType::BRANCH_FUND_DISBURSEMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id'           => $branch->id,
            'owner_type'         => TransactionOwnerType::COMPANY_BRANCH,
            'branch_id'          => $this->user['profile']['branch']['id']
        ]);
        $message     = $this->faker->realText();

        $response = $this->postGraphQL([
            'query'     => TransactionsQueriesAndMutations::processTransaction(),
            'variables' => [
                'transaction_id' => $transaction->id,
                'action'         => TransactionProcessingActions::APPROVE,
                'message'        => $message,
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'ProcessTransaction' => [
                    'id'                 => $transaction->id,
                    'transaction_amount' => 500,
                    'transaction_status' => TransactionStatus::COMPLETED
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Transaction)->getTable(), [
            'id'                  => $transaction->id,
            'transaction_status'  => TransactionStatus::COMPLETED,
            'transaction_amount'  => $transaction->transaction_amount,
            'transaction_type'    => $transaction->transaction_type,
            'transaction_purpose' => $transaction->transaction_purpose,
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id'       => $this->user['id'],
            'transaction_id'  => $transaction->id,
            'processing_type' => TransactionProcessingActions::APPROVE,
            'message'         => $message
        ]);
    }

    /**
     * @test
     */
    public function testItCorrectlyDisapprovesABranchFundReimbursementTransaction()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_ACCOUNTANT]);

        $branch = CompanyBranch::first();

        $transaction = factory(Transaction::class)->create([
            'transaction_amount' => 500,
            'transaction_type'   => TransactionType::BRANCH_FUND_DISBURSEMENT,
            'transaction_status' => TransactionStatus::PENDING,
            'owner_id'           => $branch->id,
            'owner_type'         => TransactionOwnerType::COMPANY_BRANCH,
            'branch_id'          => $this->user['profile']['branch']['id']
        ]);
        $message     = $this->faker->realText();

        $response = $this->postGraphQL([
            'query'     => TransactionsQueriesAndMutations::processTransaction(),
            'variables' => [
                'transaction_id' => $transaction->id,
                'action'         => TransactionProcessingActions::DISAPPROVE,
                'message'        => $message,
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'ProcessTransaction' => [
                    'id'                 => $transaction->id,
                    'transaction_amount' => 500,
                    'transaction_status' => TransactionStatus::FAILED
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Transaction)->getTable(), [
            'id'                  => $transaction->id,
            'transaction_status'  => TransactionStatus::FAILED,
            'transaction_amount'  => $transaction->transaction_amount,
            'transaction_type'    => $transaction->transaction_type,
            'transaction_purpose' => $transaction->transaction_purpose,
        ]);

        $this->assertDatabaseHas(with(new ProcessedTransaction())->getTable(), [
            'causer_id'       => $this->user['id'],
            'transaction_id'  => $transaction->id,
            'processing_type' => TransactionProcessingActions::DISAPPROVE,
            'message'         => $message
        ]);
    }

}
