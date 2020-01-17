<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\CustomerQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class CustomerQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers;

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
                ]
            ]
        ]);
    }
}
