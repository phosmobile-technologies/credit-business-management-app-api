<?php

namespace Tests\Feature;

use App\Models\enums\ContributionFrequency;
use App\Models\Enums\UserRoles;
use App\Models\MemberContribution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\ContributionQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class ContributionMutationsTest extends TestCase
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
    }

    public function testCreateContributionMutation() {
        $testUserDetails = $this->createLoginAndGetTestUserDetails([UserRoles::CUSTOMER]);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);

        $contributionData = factory(MemberContribution::class)->make()->toArray();
        $contributionData['user_id'] = $this->user['id'];

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

    public function testUpdateContributionMutation() {
        $testUserDetails = $this->createLoginAndGetTestUserDetails([UserRoles::ADMIN_STAFF]);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);

        $contributionData = factory(MemberContribution::class)->make()->toArray();
        $contributionData['user_id'] = $this->user['id'];
        $contribution = MemberContribution::create($contributionData);

        $contributionData = collect($contribution)->except([
            'created_at',
            'updated_at',
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
}