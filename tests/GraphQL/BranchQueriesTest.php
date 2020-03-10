<?php

namespace Tests\GraphQL;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Enums\LoanConditionStatus;
use App\Models\UserProfile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\BranchQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class BranchQueriesTest extends TestCase
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
    public function testGetBranchByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $company = Company::first();
        $branch = CompanyBranch::first();
        $users = [];

        for ($i = 0; $i < 3; $i++) {
            $user = $this->createUser();

            array_push($users, $user);
        }


        $response = $this->postGraphQL([
            'query' => BranchQueriesAndMutations::getBranchById(),
            'variables' => [
                'id' => $branch->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetBranchById' => [
                    'id' => $branch->id,
                    'customers' => [
                        ['id' => $users[0]['id']],
                        ['id' => $users[1]['id']],
                        ['id' => $users[2]['id']],
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testGetBranchCustomersQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $branch = CompanyBranch::first();
        $users = [];

        for ($i = 0; $i < 3; $i++) {
            $user = $this->createUser();

            array_push($users, $user);
        }

        $response = $this->postGraphQL([
            'query' => BranchQueriesAndMutations::getBranchCustomers(),
            'variables' => [
                'branch_id' => $branch->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetBranchCustomers' => [
                    'data' => [
                        ['id' => $users[0]['id']],
                        ['id' => $users[1]['id']],
                        ['id' => $users[2]['id']],
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testGetBranchLoansQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();
        $branch = CompanyBranch::first();
        $user = $this->createUser();
        $testLoans = [];

        for ($i = 0; $i < 3; $i++) {
            $loan = $this->createTestLoan($user);
            array_push($testLoans, $loan);
        }

        $response = $this->postGraphQL([
            'query' => BranchQueriesAndMutations::getBranchLoans(),
            'variables' => [
                'branch_id' => $branch->id
            ],
        ], $this->headers);

        $testLoanIds = [
            $testLoans[0]['id'],
            $testLoans[1]['id'],
            $testLoans[2]['id'],
        ];
        $loanIds = $response->json("data.GetBranchLoans.data.*.id");

        foreach ($testLoanIds as $testLoanId) {
            $this->assertContains($testLoanId, $loanIds);
        }
    }

    /**
     * @test
     */
    public function testGetBranchLoansQueryFiltersByLoanConditionStatus()
    {
        $this->loginTestUserAndGetAuthHeaders();
        $branch = CompanyBranch::first();
        $user = $this->createUser();
        $testLoans = [];

        for ($i = 0; $i < 30; $i++) {
            $loan = $this->createTestLoan($user);
            array_push($testLoans, $loan);
        }

        $response = $this->postGraphQL([
            'query' => BranchQueriesAndMutations::getBranchLoans(),
            'variables' => [
                'branch_id' => $branch->id,
                'loan_condition_status' => LoanConditionStatus::ACTIVE
            ],
        ], $this->headers);

        $loanStatuses = $response->json("data.GetBranchLoans.data.*.loan_condition_status");

        foreach ($loanStatuses as $loanStatus) {
            $this->assertEquals(LoanConditionStatus::ACTIVE, $loanStatus);
        }
    }

    public function testSearchBranchCustomersQuery() {
        $this->markTestSkipped("The test for searching a branch's customers needs to be implemented");
    }
}
