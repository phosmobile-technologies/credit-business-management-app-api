<?php

namespace Tests\GraphQL;

use App\Models\CustomerWithdrawalRequest;
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

        $testUser = $this->createUser();
        $testCustomerWithdrawalRequests = $this->createTestCustomerWithdrawalRequest($testUser, 3)->toArray();

        $response = $this->postGraphQL([
            'query' => CustomerWithdrawalRequestQueriesAndMutations::getCustomerWithdrawalRequestById(),
            'variables' => [
                'id' => $testUser->id,
                'user_id' => $testUser->user_id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetCustomerWithdrawalRequestById' => [
                    'id' => $testUser->id,
                    'user_id' =>$testUser->user_id
                ]
            ]
        ]);
    }
}
