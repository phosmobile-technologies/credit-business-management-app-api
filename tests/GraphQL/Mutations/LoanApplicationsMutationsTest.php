<?php

namespace Tests\GraphQL\Mutations;

use App\Models\Enums\UserRoles;
use App\Models\LoanApplication;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\LoanApplicationQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanApplicationsMutationsTest extends TestCase
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
    public function testItCreatesALoanApplication()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::CUSTOMER]);

        $loanApplicationData = factory(LoanApplication::class)->make();
        $loanApplicationData['user_id'] = $this->user['id'];

        $response = $this->postGraphQL([
            'query' => LoanApplicationQueriesAndMutations::createLoanApplication(),
            'variables' => [
                'input' => $loanApplicationData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'CreateLoanApplication' => [
                    'loan_amount' => $loanApplicationData['loan_amount'],
                    'user' => [
                        "id" => $this->user['id']
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseHas('loan_applications', [
            "user_id" => $this->user['id'],
            "loan_amount" => $loanApplicationData['loan_amount']
        ]);
    }

    /**
     * @test
     */
    public function testItDoesNotCreateLoanApplicationIfUserIsNotACustomer()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $loanApplicationData = factory(LoanApplication::class)->make();
        $loanApplicationData['user_id'] = $this->user['id'];

        $response = $this->postGraphQL([
            'query' => LoanApplicationQueriesAndMutations::createLoanApplication(),
            'variables' => [
                'input' => $loanApplicationData
            ],
        ], $this->headers);

        $response->assertJson([
            'errors' => [
                ['message' => "You are not authorized to access CreateLoanApplication"]
            ]
        ]);

        $this->assertDatabaseMissing('loan_applications', [
            "user_id" => $this->user['id'],
            "loan_amount" => $loanApplicationData['loan_amount']
        ]);
    }
}
