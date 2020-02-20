<?php

namespace Tests\Unit\Services;

use App\GraphQL\Errors\GraphqlError;
use App\Models\enums\RequestType;
use App\Models\CustomerWithdrawalRequest;
use App\Models\Transaction;
use App\Repositories\CustomerWithdrawalRequestRepository;
use App\Services\CustomerWithdrawalRequestService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class CustomerWithdrawalRequestServiceTest extends TestCase
{
    use WithFaker;

    /**
     * @var CustomerWithdrawalRequestService
     */
    private $customerwithdrawalrequestService;

    /**
     * @var CustomerWithdrawalRequestRepository
     */
    private $customerwithdrawalrequestRepository;

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

        $this->customerwithdrawalrequestRepository = Mockery::mock(CustomerWithdrawalRequestRepository::class);
        $this->transactionService = Mockery::mock(TransactionService::class);

        $this->app->instance(CustomerWithdrawalRequestRepository::class, $this->customerwithdrawalrequestRepository);
        $this->app->instance(TransactionService::class, $this->transactionService);

        $this->customerwithdrawalrequestService = $this->app->make(CustomerWithdrawalRequestService::class);
    }

    /**
     * @test
     * @throws \App\GraphQL\Errors\GraphqlError
     */
    public function testItThrowsExceptionWhenTryingToOverRepayCustomerWithdrawalRequest()
    {
        $customerwithdrawalrequest = factory(CustomerWithdrawalRequest::class)->make([
            'id' => $this->faker->uuid,
            'request_balance' => 1000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'request_amount' => 2000,
            'request_type' => RequestType::CONTRIBUTION_WITHDRAWAL,
        ])->toArray();

        $this->customerwithdrawalrequestRepository->shouldReceive('find')
            ->with($customerwithdrawalrequest->id)
            ->andReturn($customerwithdrawalrequest);

        $this->expectException(GraphqlError::class);
        $this->customerwithdrawalrequestService->initiateCustomerWithdrawalRequestRepayment($customerwithdrawalrequest->id, $transactionDetails);
    }

    /**
     * @test
     * @throws \App\GraphQL\Errors\GraphqlError
     */
    public function testItThrowsExceptionWhenTryingToRepayCustomerWithdrawalRequestWithWrongRequestType()
    {
        $customerwithdrawalrequest = factory(CustomerWithdrawalRequest::class)->make([
            'id' => $this->faker->uuid,
            'request_balance' => 1000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'request_amount' => 500,
            'request_type' => RequestType::VENDOR_PAYOUT,
        ])->toArray();

        $this->customerwithdrawalRequestRepository->shouldReceive('find')
            ->with($customerwithdrawalrequest->id)
            ->andReturn($customerwithdrawalrequest);

        $this->expectException(GraphqlError::class);
        $this->customerwithdrawalrequestService->initiateCustomerWithdrawalRequestRepayment($customerwithdrawalrequest->id, $transactionDetails);
    }

    /**
     * @test
     * @throws GraphqlError
     * @group active
     */
    public function testItCorrectlyInitiatesACustomerWithdrawalRequestRepayment()
    {
        $customerwithdrawalrequest = factory(CustomerWithdrawalRequest::class)->make([
            'id' => $this->faker->uuid,
            'request_balance' => 1000
        ]);

        $transactionDetails = factory(Transaction::class)->make([
            'request_amount' => 500,
            'request_type' => RequestType::CONTRIBUTION_WITHDRAWAL,
        ])->toArray();

        $this->customerwithdrawalrequestRepository->shouldReceive('find')
            ->with($customerwithdrawalrequest->id)
            ->andReturn($customerwithdrawalrequest);

        $this->transactionService->shouldReceive('initiateCustomerWithdrawalRequestRepaymentTransaction')
            ->andReturnUsing(function ($customerwithdrawalrequestArgument, $transactionDetailsArgument) use ($customerwithdrawalrequest, $transactionDetails) {
                $this->assertEquals($customerwithdrawalrequest, $customerwithdrawalrequestArgument);
                $this->assertEquals($transactionDetails, $transactionDetailsArgument);
            });

        $this->customerwithdrawalrequestService->initiateCustomerWithdrawalRequestRepayment($customerwithdrawalrequest->id, $transactionDetails);
    }
}
