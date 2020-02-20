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
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestCustomerWithdrawalRequests, InteractsWithTestTransactions, WithFaker;

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

        $customerwithdrawalrequest = factory(C::class)->states('with_default_values')->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'request_balance' => 2000
        ]);

        $customerwithdrawalrequest = factory(Transaction::class)->create([
            'Request_status' => RequestStatus::PENDING,
            'request_type' => RequestType::CONTRIBUTION_WITHDRAWAL,
            'owner_id' => $customerwithdrawalrequest->id
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
