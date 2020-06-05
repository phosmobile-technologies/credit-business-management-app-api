<?php

namespace Tests\GraphQL\Mutations;

use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\UserRoles;
use App\Models\Loan;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\GraphQL\Helpers\Schema\LoanDisbursementQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanDisbursementTest extends TestCase
{
    use InteractsWithTestLoans, InteractsWithTestUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testItDisbursesLoanCorrectly()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_ACCOUNTANT]);

        $loan = factory(Loan::class)->states('with_default_values')->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 0,
            'disbursement_status' => DisbursementStatus::NOT_DISBURSED,
            'amount_disbursed' => null,
            'interest_rate' => 10,
            'tenure' => 5
        ]);

        $loanDisbursementInput = [
            'loan_id' => $loan->id,
            'amount_disbursed' => 1000,
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
                    "amount_disbursed" => 1000,
                    "loan_balance" => 1300,
                    "loan_amount" => 1000,
                    "totalInterestAmount" => 300,
                    "nextDueAmount" => 260,
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testItDoesNotDisburseAmountGreaterThanLoanBalance()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_ACCOUNTANT]);

        $loan = factory(Loan::class)->states('with_default_values')->create([
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
    public function testItDoesNotDisburseMoneyForUnapprovedLoans()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_ACCOUNTANT]);

        $loan = factory(Loan::class)->states('with_default_values')->create([
            'application_status' => LoanApplicationStatus::PENDING(),
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 1000,
            'amount_disbursed' => 0
        ]);

        $loanDisbursementInput = [
            'loan_id' => $loan->id,
            'amount_disbursed' => 600,
            'message' => 'We are only able to disburse 600 for now'
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
        $this->loginTestUserAndGetAuthHeaders([UserRoles::CUSTOMER]);

        $loan = factory(Loan::class)->states('with_default_values')->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 1000,
            'amount_disbursed' => 0
        ]);

        $loanDisbursementInput = [
            'loan_id' => $loan->id,
            'amount_disbursed' => 1600,
            'message' => 'We are only able to disburse 1600 for now'
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
