<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\LoanQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWIthTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanQueriesTest extends TestCase
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
    public function testGetLoanByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $loan = $this->createTestLoan();


        $response = $this->postGraphQL([
            'query' => LoanQueriesAndMutations::GetLoanById(),
            'variables' => [
                'id' => $loan->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetLoanById' => [
                    'id' => $loan->id
                ]
            ]
        ]);
    }
}
