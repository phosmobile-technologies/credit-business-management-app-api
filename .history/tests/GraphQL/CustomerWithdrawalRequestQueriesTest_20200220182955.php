<?php

namespace Tests\GraphQL;

use App\Models\CustomerWithdrawalRequest;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\RequestStatus;
use App\Models\enums\RequestType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\CustomerWithdrawalRequestQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestCustomerWithdrawalRequests;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestTransactions;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class CustomerWithdrawalRequestQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestCustomerWithdrawalRequests, InteractsWithTestTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testGetCustomerWithdrawalRequestByIdQuery()
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
            'query' => CustomerWithdrawalRequestQueriesAndMutations::getCustomerWithdrawalRequestById(),
            'variables' => [
                'id' => $customerwithdrawalrequest->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetCustomerWithdrawalRequestById' => [
                    'id' => $customerwithdrawalrequest->id,
                ]
            ]
        ]);
    }
}
