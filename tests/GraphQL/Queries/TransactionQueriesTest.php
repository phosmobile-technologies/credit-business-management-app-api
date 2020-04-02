<?php

namespace Tests\GraphQL\Queries;

use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\TransactionsQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class TransactionQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestLoans, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testGetTransactionByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $loan = factory(Loan::class)->states('with_default_values')->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'loan_balance' => 2000
        ]);

        $transaction = factory(Transaction::class)->create([
            'transaction_status' => TransactionStatus::PENDING,
            'owner_type' => TransactionOwnerType::LOAN,
            'owner_id' => $loan->id
        ]);


        $response = $this->postGraphQL([
            'query' => TransactionsQueriesAndMutations::GetTransactionById(),
            'variables' => [
                'id' => $transaction->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetTransactionById' => [
                    'id' => $transaction->id,
                ]
            ]
        ]);
    }
}
