<?php

namespace Tests\Feature;

use App\Models\enums\TransactionType;
use App\Models\Enums\UserRoles;
use App\Models\Loan;
use App\Models\Transaction;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Schema\TransactionsQueriesAndMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanRepaymentTransactionsTest extends TestCase
{

    use RefreshDatabase, InteractsWithTestUsers, WithFaker;

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

    /**
     * @test
     */
    public function testItInitiatesLoanRepaymentTransactionSuccessfully() {
        $testUserDetails = $this->createLoginAndGetTestUserDetails([UserRoles::CUSTOMER]);
        $this->user = $testUserDetails['user'];
        $accessToken = $testUserDetails['access_token'];
        $this->headers = $this->getGraphQLAuthHeader($accessToken);

        $loan = factory(Loan::class)->create([
            'id' => $this->faker->uuid,
            'user_id' => $this->user['id'],
            'loan_balance' => 2000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
        ])->toArray();

        $transactionData = [
            'loan_id' => $loan->id,
            'transaction_details' => $transactionDetails
        ];

        $response = $this->postGraphQL([
            'query' => TransactionsQueriesAndMutations::initiateLoanRepaymentTransaction(),
            'variables' => [
                'input' => $transactionData
            ],
        ], $this->headers);

        $response->assertJson([
            'data' => [
                'InitiateLoanRepaymentTransaction' => [
                    'transaction_amount' => 500,
                ]
            ]
        ]);

        $this->assertDatabaseHas(with (new Transaction)->getTable(), [
            'transaction_amount' => $transactionDetails['transaction_amount'],
            'transaction_type' => $transactionDetails['transaction_type'],
            'transaction_purpose' => $transactionDetails['transaction_purpose'],
        ]);
    }
}
