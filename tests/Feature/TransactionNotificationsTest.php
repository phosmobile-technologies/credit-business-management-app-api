<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\UserProfile;
use App\Notifications\TransactionProcessedNotification;
use App\Services\TransactionService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestTransactions;
use Tests\TestCase;

class TransactionNotificationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestTransactions;

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

        $this->seed('TestDatabaseSeeder');
        $this->transactionService = $this->app->make(TransactionService::class);
    }

    /**
     * @test
     */
    public function testItSendsANotificationWhenLoanRepaymentIsProcessed()
    {
        Notification::fake();

        $company = Company::first();
        $branch = CompanyBranch::first();

        $user = factory(User::class)->create();
        $user->profile()->save(
            factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ])
        );
        $user->user_profile_id = $user->profile->id;
        $user->save();


        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER,
            'user_id' => $user->id,
            'loan_amount' => 1000,
            'loan_balance' => 500
        ]);
        $transaction =factory(Transaction::class)->create([
            'transaction_status' => TransactionStatus::PENDING,
            'owner_type' => TransactionOwnerType::LOAN,
            'owner_id' => $loan->id,
            'transaction_type' => TransactionType::LOAN_REPAYMENT,
            'transaction_amount' => 100,
            'branch_id' => $user->profile->branch->id
        ]);
        $message = "Approved by manager";

        $this->transactionService->processTransaction($user, $transaction->id, TransactionProcessingActions::APPROVE, $message);

        Notification::assertSentTo(
            $user,
            TransactionProcessedNotification::class,
            function ($notification, $channels) use ($transaction) {
                return $notification->transaction->id === $transaction->id;
            }
        );
    }

}
