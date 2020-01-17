<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\UserProfile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\BranchQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class BranchQueriesTest extends TestCase
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

        $company = Company::first();
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
}
