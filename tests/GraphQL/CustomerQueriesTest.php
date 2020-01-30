<?php

namespace Tests\GraphQL;

use App\Models\ContributionPlan;
use App\Models\enums\TransactionOwnerType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\CustomerQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestContributionPlans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestTransactions;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class CustomerQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestLoans, InteractsWithTestTransactions, InteractsWithTestContributionPlans;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testGetClientByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $testUser = $this->createUser();
        $testLoans = $this->createTestLoan($testUser, 3)->toArray();

        $response = $this->postGraphQL([
            'query' => CustomerQueriesAndMutations::getClientById(),
            'variables' => [
                'id' => $testUser->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetCustomerById' => [
                    'id' => $testUser->id,
                    'loans' => [
                        ['id' => $testLoans[0]['id']],
                        ['id' => $testLoans[1]['id']],
                        ['id' => $testLoans[2]['id']],
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     */
    public function testGetCustomerLoanTransactionsByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $transactions = [];
        $contributionPlans = factory(ContributionPlan::class, 3)->create([
           'user_id' => $this->user['id'],
        ]);

        foreach ($contributionPlans as $contributionPlan) {
            $transaction = $this->createTransaction(TransactionOwnerType::CONTRIBUTION_PLAN, $contributionPlan['id'])->toArray();
            array_push($transactions, $transaction);
        }

        $testTransactionIds = [
            $transactions[0]['id'],
            $transactions[1]['id'],
            $transactions[2]['id'],
        ];

        $response = $this->postGraphQL([
            'query' => CustomerQueriesAndMutations::GetCustomerTransactionsById(),
            'variables' => [
                'customer_id' => $this->user['id'],
                'transaction_type' => TransactionOwnerType::CONTRIBUTION_PLAN
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetCustomerTransactionsById' => [
                    'data' => [
                        ['id' => $transactions[0]['id']],
                        ['id' => $transactions[1]['id']],
                        ['id' => $transactions[2]['id']],
                    ]
                ]
            ]
        ]);

        $transactionIds = $response->json("data.GetCustomerTransactionsById.data.*.id");

        foreach ($transactionIds as $transactionId) {
            $this->assertContains($transactionId, $testTransactionIds);
        }
    }

    /**
     * @test
     *
     */
    public function testGetCustomerContributionPlanTransactionsByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $testUser = $this->createUser();
        $testLoans = $this->createTestLoan($testUser, 3)->toArray();
        $transactions = [];

        foreach ($testLoans as $testLoan) {
            $transaction = $this->createTransaction(TransactionOwnerType::LOAN, $testLoan['id'])->toArray();
            array_push($transactions, $transaction);
        }

        $testTransactionIds = [
            $transactions[0]['id'],
            $transactions[1]['id'],
            $transactions[2]['id'],
        ];

        $response = $this->postGraphQL([
            'query' => CustomerQueriesAndMutations::GetCustomerTransactionsById(),
            'variables' => [
                'customer_id' => $testUser->id,
                'transaction_type' => TransactionOwnerType::LOAN
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetCustomerTransactionsById' => [
                    'data' => [
                        ['id' => $transactions[0]['id']],
                        ['id' => $transactions[1]['id']],
                        ['id' => $transactions[2]['id']],
                    ]
                ]
            ]
        ]);

        $transactionIds = $response->json("data.GetCustomerTransactionsById.data.*.id");

        foreach ($transactionIds as $transactionId) {
            $this->assertContains($transactionId, $testTransactionIds);
        }
    }
}
