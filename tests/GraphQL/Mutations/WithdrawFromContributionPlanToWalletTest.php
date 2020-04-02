<?php

namespace Tests\GraphQL\Mutations;

use App\Models\ContributionPlan;
use App\Models\enums\ContributionType;
use App\Models\Enums\UserRoles;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\ContributionQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestContributionPlans;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestWallets;
use Tests\TestCase;

class WithdrawFromContributionPlanToWalletTest extends TestCase
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
    public function testItCanWithdrawFundsFromContributionPlanToWallet() {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::CUSTOMER]);

        $contributionPlan = factory(ContributionPlan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'goal' => 20000,
            'balance' => 15000,
            'payback_date' => Carbon::tomorrow(),
            'type' => ContributionType::FIXED,
            'activation_date' => Carbon::today()->subDays(100)
        ]);

        $wallet = factory(Wallet::class)->create([
            'user_id' => $this->user['id'],
            'wallet_balance' => 1000
        ]);

        $mutationInput = [
            'contribution_plan_id' => $contributionPlan->id,
            'wallet_id' => $wallet->id,
            'amount' => 100
        ];

        $response = $this->postGraphQL([
            'query' => ContributionQueriesAndMutations::withdrawFromContributionPlanToWallet(),
            'variables' => [
                'input' => $mutationInput
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'WithdrawFromContributionPlanToWallet' => [
                    'balance' => 14900,
                    'goal' => 20000,
                ]
            ]
        ]);

        $this->assertDatabaseHas(with(new Wallet)->getTable(), [
            'id' => $wallet->id,
            'wallet_balance' => 1100
        ]);
    }
}
