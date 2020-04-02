<?php

namespace Tests\GraphQL\Queries;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\BranchQueriesAndMutations;
use Tests\GraphQL\Helpers\Schema\LoanApplicationQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestLoans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanApplicationsQueriesTest extends TestCase
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
    public function testGetBranchLoanApplicationsQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();
        $company = Company::first();
        $branch = CompanyBranch::first();
        $user = $this->createUser();

        $loanApplications = factory(LoanApplication::class, 2)->create([
            'user_id' => $user->id
        ])->toArray();
        $testLoanApplicationIds = [
            $loanApplications[0]['id'],
            $loanApplications[1]['id'],
        ];

        $branchTwo = factory(CompanyBranch::class)->create([
            'company_id' => $company->id
        ]);
        $userTwo = $this->createUser($branchTwo->id);
        $otherLoanApplications = factory(LoanApplication::class, 2)->create([
            'user_id' => $userTwo->id
        ])->toArray();

        $response = $this->postGraphQL([
            'query' => LoanApplicationQueriesAndMutations::getBranchLoanApplications(),
            'variables' => [
                'branch_id' => $branch->id
            ],
        ], $this->headers);

        $loanApplicationIds = $response->json("data.GetBranchLoanApplications.data.*.id");

        foreach ($loanApplicationIds as $loanApplicationId) {
            $this->assertContains($loanApplicationId, $testLoanApplicationIds);
        }
    }
}
