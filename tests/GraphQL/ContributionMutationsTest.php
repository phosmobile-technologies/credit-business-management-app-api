<?php

namespace Tests\GraphQL;

use App\Models\enums\ContributionFrequency;
use App\Models\Enums\UserRoles;
use App\Models\ContributionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\ContributionQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class ContributionMutationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    public function testCreateContributionMutation() {
        $this->loginTestUserAndGetAuthHeaders();

        $contributionData = factory(ContributionPlan::class)->make()->toArray();
        $contributionData['user_id'] = $this->user['id'];

        $contributionData =  collect($contributionData)->except([
            'contribution_balance',
            'contribution_interest_rate',
        ])->toArray();



        $response = $this->postGraphQL([
            'query' => ContributionQueriesAndMutations::createContribution(),
            'variables' => [
                'input' => $contributionData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'CreateContribution' => [
                    'contribution_type' => $contributionData['contribution_type'],
                    'contribution_frequency' => $contributionData['contribution_frequency'],
                    'contribution_amount' => $contributionData['contribution_amount'],
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testUpdateContributionMutation() {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $contributionData = factory(ContributionPlan::class)->make()->toArray();
        $contributionData['user_id'] = $this->user['id'];
        $contribution = ContributionPlan::create($contributionData);

        $contributionData = collect($contribution)->except([
            'created_at',
            'updated_at',
            'contribution_balance',
            'contribution_interest_rate',
            'user_id'
        ])->toArray();
        $contributionData['contribution_amount'] = 2500;
        $contributionData['contribution_frequency'] = ContributionFrequency::DAILY;

        $response = $this->postGraphQL([
            'query' => ContributionQueriesAndMutations::updateContribution(),
            'variables' => [
                'input' => $contributionData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'UpdateContribution' => [
                    'contribution_type' => $contributionData['contribution_type'],
                    'contribution_frequency' => ContributionFrequency::DAILY,
                    'contribution_amount' => 2500,
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testDeleteContributionMutation() {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $contributionData = factory(ContributionPlan::class)->make()->toArray();
        $contributionData['user_id'] = $this->user['id'];
        $contribution = ContributionPlan::create($contributionData);

        $response = $this->postGraphQL([
            'query' => ContributionQueriesAndMutations::deleteContribution(),
            'variables' => [
                'contribution_id' => $contribution->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'DeleteContribution' => [
                    'contribution_type' => $contributionData['contribution_type'],
                    'contribution_frequency' => $contributionData['contribution_frequency'],
                    'contribution_amount' => $contributionData['contribution_amount'],
                ]
            ]
        ]);
    }

}
