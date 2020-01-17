<?php

namespace Tests\GraphQL;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\CustomerQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWIthTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class CustomerQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWIthTestLoans;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testGetClientByIdQuery() {
        $this->loginTestUserAndGetAuthHeaders();

        $testUser = $this->createUser();
        $testLoans = [];
        for ($i = 0; $i < 3; $i++) {
            $loan = $this->createTestLoan($testUser);
            array_push($testLoans, $loan);
        }

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
}
