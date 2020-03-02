<?php

namespace Tests\GraphQL;

use App\Models\enums\TransactionOwnerType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\WalletQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestWallets;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestTransactions;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class WalletQueriesTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers, InteractsWithTestWallets, InteractsWithTestTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     */
    public function testGetWalletByIdQuery()
    {
        $this->loginTestUserAndGetAuthHeaders();

        $wallet = $this->createTestWallet();
        $transaction = $this->createTransaction(TransactionOwnerType::WALLET, $wallet->id);

        $response = $this->postGraphQL([
            'query' => WalletQueriesAndMutations::GetWalletById(),
            'variables' => [
                'id' => $wallet->id
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'GetWalletById' => [
                    'id' => $wallet->id,
                    'transactions' => [
                        ['id' => $transaction->id]
                    ]
                ]
            ]
        ]);
    }
}
