<?php

namespace Tests\GraphQL;

use App\Models\Enums\UserRoles;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\WalletQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class WalletMutationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    public function testCreateWalletMutation()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $walletData = factory(Wallet::class)->make()->toArray();
        $walletData['user_id'] = $this->user['id'];

        $response = $this->postGraphQL([
            'query' => WalletQueriesAndMutations::createWallet(),
            'variables' => [
                'input' => $walletData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'CreateWallet' => [
                    'wallet_amount' => $walletData['wallet_amount'],
                    'wallet_balance' => $walletData['wallet_balance'],
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function testUpdateWalletMutation()
    {
        $this->loginTestUserAndGetAuthHeaders([UserRoles::ADMIN_STAFF]);

        $walletData = factory(Wallet::class)->make()->toArray();
        $walletData['user_id'] = $this->user['id'];
        $wallet = Wallet::create($walletData);

        $walletData = collect($wallet)->except([
            'created_at',
            'updated_at',
            'user_id'
        ])->toArray();
        $walletData['wallet_amount'] = 2500;

        $response = $this->postGraphQL([
            'query' => WalletQueriesAndMutations::updateWallet(),
            'variables' => [
                'input' => $walletData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'UpdateWallet' => [
                    'wallet_amount' => $walletData['wallet_amount'],
                    'wallet_amount' => 2500,
                ]
            ]
        ]);
    }
}
