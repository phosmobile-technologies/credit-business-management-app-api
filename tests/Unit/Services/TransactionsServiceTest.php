<?php

namespace Tests\Unit;

use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class TransactionsServiceTest extends TestCase
{
    use WithFaker;

    /**
     * @var TransactionService
     */
    private $transactionsService;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = Mockery::mock(TransactionRepository::class);

        $this->app->instance(TransactionRepository::class, $this->transactionRepository);

        $this->transactionsService = $this->app->make(TransactionService::class);
    }

    /**
     * @test
     */
    public function testItCreatesLoanRepaymentTransactionSuccessfully()
    {
        $loan = factory(Loan::class)->make([
            'id' => $this->faker->uuid,
            'loan_balance' => 1000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'owner_id' => $loan->id,
            'owner_type' => TransactionOwnerType::LOAN,
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
            'transaction_status' => TransactionStatus::PENDING
        ]);

        $this->transactionRepository->shouldReceive('create')
            ->andReturnUsing(function ($arg1) use ($transactionDetails) {
                $this->assertEquals($transactionDetails->toArray(), $arg1);

                // Since the factory created an instance of Transaction
                $transaction = $transactionDetails;
                return $transaction;
            });

        $this->transactionsService->initiateLoanRepaymentTransaction($loan->id, $transactionDetails->toArray());
    }
}
