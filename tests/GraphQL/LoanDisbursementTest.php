<?php

namespace Tests\GraphQL;

use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\UserRoles;
use App\Models\Loan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\GraphQL\Helpers\Schema\LoanDisbursementQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWIthTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanDisbursementTest extends TestCase
{
    use InteractsWIthTestLoans, InteractsWithTestUsers, RefreshDatabase;

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

        $testUserDetails = $this->createLoginAndGetTestUserDetails([UserRoles::ADMIN_ACCOUNTANT]);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);
    }

    /**
     * @test
     */
    public function testItDisbursesLoanCorrectly()
    {
        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 1000,
            'amount_disbursed' => 0
        ]);

        $loanDisbursementInput = [
            'loan_id' => $loan->id,
            'amount_disbursed' => 600,
            'message' => 'We are only able to disburse 500 for now'
        ];

        $response = $this->postGraphQL([
            'query' => LoanDisbursementQueriesAndMutations::disburseLoan(),
            'variables' => [
                'input' => $loanDisbursementInput
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'DisburseLoan' => [
                    "id" => $loan->id,
                    "amount_disbursed" => 600,
                    "loan_balance" => 400,
                    "loan_amount" => 1000
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testItDoesNotDisburseAmountGreaterThanLoanBalance()
    {
        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 1000,
            'amount_disbursed' => 0
        ]);

        $loanDisbursementInput = [
            'loan_id' => $loan->id,
            'amount_disbursed' => 1600,
            'message' => 'We are only able to disburse 500 for now'
        ];

        $response = $this->postGraphQL([
            'query' => LoanDisbursementQueriesAndMutations::disburseLoan(),
            'variables' => [
                'input' => $loanDisbursementInput
            ],
        ], $this->headers);

        $errors = $response->json("errors");

        $this->assertIsString($errors[0]['message']);
    }

    /**
     * @test
     */
    public function testItThrowsErrorForNoneAuthorizedUsersTryingToDisburseALoan()
    {
        $testUserDetails = $this->createLoginAndGetTestUserDetails([UserRoles::CUSTOMER]);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);

        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 1000,
            'amount_disbursed' => 0
        ]);

        $loanDisbursementInput = [
            'loan_id' => $loan->id,
            'amount_disbursed' => 1600,
            'message' => 'We are only able to disburse 500 for now'
        ];

        $response = $this->postGraphQL([
            'query' => LoanDisbursementQueriesAndMutations::disburseLoan(),
            'variables' => [
                'input' => $loanDisbursementInput
            ],
        ], $this->headers);

        $response->assertJson([
            'errors' => [
                ['message' => "You are not authorized to access DisburseLoan"]
            ]
        ]);
    }
}
