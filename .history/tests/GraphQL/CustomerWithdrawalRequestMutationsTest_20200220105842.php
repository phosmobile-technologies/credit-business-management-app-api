<?php

namespace Tests\GraphQL;

use App\Models\enums\RequestStatus;
use App\Models\enums\RequestType;
use App\Models\Enums\UserRoles;
use App\Models\CustomerWithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\CustomerWithdrawalRequestQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class CustomerWithdrawalRequestMutationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    public function testCreateCustomerWithdrawalRequestMutation()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $customerwithdrawalrequestData = factory(CustomerWithdrawalRequest::class)->make()->toArray();
        $customerwithdrawalrequestData['user_id'] = $this->user['id'];

        $response = $this->postGraphQL([
            'query' => CustomerWithdrawalRequestQueriesAndMutations::createCustomerWithdrawalRequest(),
            'variables' => [
                'input' => $customerwithdrawalrequestData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'CreateCustomerWithdrawalRequest' => [
                    'request_type' => $customerwithdrawalrequestData['request_type'],
                    'request_status' => $customerwithdrawalrequestData['request_status'],
                    'request_amount' => $customerwithdrawalrequestData['request_amount'],
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testUpdateCustomerWithdrawalRequestMutation()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $customerwithdrawalrequestData = factory(CustomerWithdrawalRequest::class)->make()->toArray();
        $customerwithdrawalrequestData['user_id'] = $this->user['id'];
        $customerwithdrawalrequest = CustomerWithdrawalRequest::create($customerwithdrawalrequestData);

        $customerwithdrawalrequestData = collect($customerwithdrawalrequest)->except([
            'created_at',
            'updated_at',
            'user_id'
        ])->toArray();
        $customerwithdrawalrequestData['request_amount'] = 2500;
        $customerwithdrawalrequestData['request_status'] = RequestStatus::APPROVED_BY_BRANCH_MANAGER;

        $response = $this->postGraphQL([
            'query' => CustomerWithdrawalRequestQueriesAndMutations::updateCustomerWithdrawalRequest(),
            'variables' => [
                'input' => $customerwithdrawalrequestData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'UpdateCustomerWithdrawalRequest' => [
                    'request_type' => $customerwithdrawalrequestData['request_type'],
                    'request_status' => RequestStatus::APPROVED_BY_BRANCH_MANAGER,
                    'request_amount' => 2500,
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testDeleteCustomerWithdrawalRequestMutation()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::a]);

        $customerwithdrawalrequestData = factory(CustomerWithdrawalRequest::class)->make()->toArray();
        $customerwithdrawalrequestData['user_id'] = $this->user['id'];
        $customerwithdrawalrequest = CustomerWithdrawalRequest::create($customerwithdrawalrequestData);

        $response = $this->postGraphQL([
            'query' => CustomerWithdrawalRequestQueriesAndMutations::deleteCustomerWithdrawalRequest(),
            'variables' => [
                'user_id' => $customerwithdrawalrequest->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'DeleteCustomerWithdrawalRequest' => [
                    'request_type' => $customerwithdrawalrequestData['request_type'],
                    'request_status' => $customerwithdrawalrequestData['request_status'],
                    'request_amount' => $customerwithdrawalrequestData['request_amount'],
                ]
            ]
        ]);
    }

    public function testItCreatesACustomerWithdrawalRequest()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::CUSTOMER]);

        $customerwithdrawalrequestData = factory(CustomerWithdrawalRequest::class)->make();
        $customerwithdrawalrequestData['user_id'] = $this->user['id'];

        $response = $this->postGraphQL([
            'query' => CustomerWithdrawalRequestQueriesAndMutations::createCustomerWithdrawalRequest(),
            'variables' => [
                'input' => $customerwithdrawalrequestData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'CreateCustomerWithdrawalRequest' => [
                    'request_amount' => $customerwithdrawalrequestData['request_amount'],
                    'user' => [
                        "id" => $this->user['id']
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseHas('customer_withdrawal_requests', [
            "user_id" => $this->user['id'],
            "request_amount" => $customerwithdrawalrequestData['request_amount']
        ]);
    }
}
