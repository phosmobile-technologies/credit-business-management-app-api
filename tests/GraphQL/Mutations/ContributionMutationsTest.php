<?php

namespace Tests\GraphQL\Mutations;

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
            'balance',
            'interest_rate',
            'status'
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
                    'type' => $contributionData['type'],
                    'frequency' => $contributionData['frequency'],
                    'goal' => $contributionData['goal'],
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
            'user_id',
            'balance',
            'interest_rate',
            'status'
        ])->toArray();
        $contributionData['goal'] = 2500;
        $contributionData['frequency'] = ContributionFrequency::DAILY;

        $response = $this->postGraphQL([
            'query' => ContributionQueriesAndMutations::updateContribution(),
            'variables' => [
                'input' => $contributionData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'UpdateContribution' => [
                    'type' => $contributionData['type'],
                    'frequency' => ContributionFrequency::DAILY,
                    'goal' => 2500,
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
                    'type' => $contributionData['type'],
                    'frequency' => $contributionData['frequency'],
                    'goal' => $contributionData['goal'],
                ]
            ]
        ]);
    }

}
