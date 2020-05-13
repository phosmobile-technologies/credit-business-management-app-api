<?php

namespace Tests\GraphQL\Mutations;

use App\Models\ContributionPlan;
use App\Models\Enums\UserRoles;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\ContributionQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestContributionPlans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestWallets;
use Tests\TestCase;

class FundContributionPlanFromWalletTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestContributionPlans, InteractsWithTestWallets, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testItCorrectlyDebitsAWalletAndCreditsAContributionPlan() {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::CUSTOMER]);

        $contributionPlan = factory(ContributionPlan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 2000,
            'balance' => 1000,
            'status' => ContributionPlan::STATUS_INACTIVE,
            'activation_date' => null
        ]);

        $wallet = factory(Wallet::class)->create([
            'user_id' => $this->user['id'],
            'wallet_balance' => 3000
        ]);

        $mutationInput = [
          'contribution_plan_id' => $contributionPlan->id,
          'wallet_id' => $wallet->id,
          'amount' => 500
        ];

        $response = $this->postGraphQL([
            'query' => ContributionQueriesAndMutations::fundContributionPlanFromWallet(),
            'variables' => [
                'input' => $mutationInput
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'FundContributionPlanFromWallet' => [
                    'balance' => 1500,
                    'goal' => 2000,
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Wallet)->getTable(), [
            'id' => $wallet->id,
            'wallet_balance' => 2500
        ]);
    }
}
