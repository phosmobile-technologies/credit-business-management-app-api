<?php

namespace Tests\Unit\Services;

use App\GraphQL\Errors\GraphqlError;
use App\Models\enums\TransactionMedium;
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Models\Transaction;
use App\Repositories\LoanRepository;
use App\Services\LoanService;
use App\Services\LoanRepaymentTransactionService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
    use WithFaker;

    /**
     * @var LoanService
     */
    private $loanService;

    /**
     * @var LoanRepository
     */
    private $loanRepository;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loanRepository = Mockery::mock(LoanRepository::class);
        $this->transactionService = Mockery::mock(TransactionService::class);

        $this->app->instance(LoanRepository::class, $this->loanRepository);
        $this->app->instance(TransactionService::class, $this->transactionService);

        $this->loanService = $this->app->make(LoanService::class);
    }

    /**
     * @test
     * @throws \App\GraphQL\Errors\GraphqlError
     */
    public function testItThrowsExceptionWhenTryingToOverRepayLoan()
    {
        $loan = factory(Loan::class)->make([
            'id' => $this->faker->uuid,
            'loan_balance' => 1000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'transaction_amount' => 2000,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
        ])->toArray();

        $this->loanRepository->shouldReceive('find')
            ->with($loan->id)
            ->andReturn($loan);

        $this->expectException(GraphqlError::class);
        $this->loanService->initiateLoanRepayment($loan->id, $transactionDetails);
    }

    /**
     * @test
     * @throws \App\GraphQL\Errors\GraphqlError
     */
    public function testItThrowsExceptionWhenTryingToRepayLoanWithWrongTransactionType()
    {
        $loan = factory(Loan::class)->make([
            'id' => $this->faker->uuid,
            'loan_balance' => 1000
        ]);
        $loanID = $this->faker->uuid;

        $transactionDetails = factory(Transaction::class)->make([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::VENDOR_PAYOUT,
        ])->toArray();

        $this->loanRepository->shouldReceive('find')
            ->with($loan->id)
            ->andReturn($loan);

        $this->expectException(GraphqlError::class);
        $this->loanService->initiateLoanRepayment($loan->id, $transactionDetails);
    }

    /**
     * @test
     * @throws GraphqlError
     */
    public function testItCorrectlyInitiatesALoanRepayment() {
        $loan = factory(Loan::class)->make([
            'id' => $this->faker->uuid,
            'loan_balance' => 1000
        ]);
        $loanID = $this->faker->uuid;

        $transactionDetails = factory(Transaction::class)->make([
            'transaction_amount' => 500,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
        ])->toArray();

        $this->loanRepository->shouldReceive('find')
            ->with($loan->id)
            ->andReturn($loan);

        $this->transactionService->shouldReceive('initiateLoanRepaymentTransaction')
            ->withArgs([$loan, $transactionDetails]);

        $this->loanService->initiateLoanRepayment($loan->id, $transactionDetails);
    }
}
