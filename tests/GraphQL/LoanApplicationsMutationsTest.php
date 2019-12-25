<?php

namespace Tests\GraphQL;

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

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');

        $testUserDetails = $this->createLoginAndGetTestUserDetails();
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);
    }

    /**
     * @test
     */
    public function testItCreatesALoanApplication()
    {
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
        $testUserDetails = $this->createLoginAndGetTestUserDetails([UserRoles::ADMIN_STAFF]);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);

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
