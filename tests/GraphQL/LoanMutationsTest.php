<?php

namespace Tests\GraphQL;

use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\Enums\UserRoles;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\LoanQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanMutationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestLoans;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testItSuccessfullyCreatesANewLoan()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $loanData = collect(factory(Loan::class)->make())
            ->except(['loan_identifier'])
            ->toArray();
        $loanData['user_id'] = $this->user['id'];
        dd($loanData);

        $response = $this->postGraphQL([
            'query' => LoanQueriesAndMutations::CreateLoanMutation(),
            'variables' => [
                'input' => $loanData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'CreateLoan' => [
                    'loan_amount' => $loanData['loan_amount']
                ]
            ]
        ]);

        $createdLoan = $response->json("data.CreateLoan");

        $this->assertEquals(DisbursementStatus::NOT_DISBURSED, $createdLoan['disbursement_status']);
        $this->assertEquals(LoanApplicationStatus::PENDING, $createdLoan['application_status']);
        $this->assertEquals(LoanConditionStatus::INACTIVE, $createdLoan['loan_condition_status']);
        $this->assertEquals(LoanDefaultStatus::NOT_DEFAULTING, $createdLoan['loan_default_status']);
    }

    /**
     * @test
     */
    public function testItCanUpdateTheLoanApplicationStatus() {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::BRANCH_MANAGER]);

        $loan = $this->createTestLoan();

        $response = $this->postGraphQL([
           'query' => LoanQueriesAndMutations::UpdateLoanApplicationStatus(),
            'variables' => [
                'loan_id' => $loan->id,
                'application_status' => LoanApplicationStatus::APPROVED_BY_BRANCH_MANAGER(),
                'message' => "The loan looks good and viable"
            ]
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'UpdateLoanApplicationStatus' => [
                    'application_status' => LoanApplicationStatus::APPROVED_BY_BRANCH_MANAGER()
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testItThrowsErrorWhenTryingToCreateALoanForAUserWithAnExistingActiveLoan() {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $loan = factory(Loan::class)->states('with_default_values')->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER(),
            "loan_condition_status" => LoanConditionStatus::ACTIVE,
            'user_id' => $this->user['id'],
            'loan_amount' => 1000,
            'loan_balance' => 1000,
            'amount_disbursed' => 0
        ]);

        $loanData = collect(factory(Loan::class)->make())
            ->except(['loan_identifier'])
            ->toArray();
        $loanData['user_id'] = $this->user['id'];

        $response = $this->postGraphQL([
            'query' => LoanQueriesAndMutations::CreateLoanMutation(),
            'variables' => [
                'input' => $loanData
            ],
        ], $this->headers);

        $response->assertJson([
            'errors' => [
                ['message' => 'This user already has an active loan and cannot take a new loan']
            ]
        ]);
    }

}
