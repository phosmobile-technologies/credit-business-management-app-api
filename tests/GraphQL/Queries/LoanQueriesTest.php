<?php

namespace Tests\GraphQL\Queries;

use App\Models\enums\TransactionOwnerType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\LoanQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestTransactions;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestLoans, InteractsWithTestTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testGetLoanByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $loan = $this->createTestLoan();
        $transaction = $this->createTransaction(TransactionOwnerType::LOAN, $loan->id);

        $response = $this->postGraphQL([
            'query' => LoanQueriesAndMutations::GetLoanById(),
            'variables' => [
                'id' => $loan->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetLoanById' => [
                    'id' => $loan->id,
                    'transactions' => [
                        ['id' => $transaction->id]
                    ]
                ]
            ]
        ]);
    }
}
