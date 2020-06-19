<?php

namespace Tests\Feature;

use App\Models\Enums\LoanConditionStatus;
use App\Models\enums\TransactionMedium;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Models\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestDatabaseSeeder');
    }

    /**
     * @test
     * @group activex
     */
    public function testItCorrectlyGetsNextDuePaymentDate() {
        $today = Carbon::today();

        $user = factory(User::class)->create();
        $loanOne = factory(Loan::class)->create([
            'due_date' => Carbon::today()->addMonths(5),
            'loan_condition_status' => LoanConditionStatus::ACTIVE,
            'amount_disbursed' => 1000,
            'interest_rate' => 10,
            'tenure' => 5,
            'user_id' => $user->id
        ]);
        $loanTwo = factory(Loan::class)->create([
            'due_date' => Carbon::today()->addMonths(5),
            'loan_condition_status' => LoanConditionStatus::ACTIVE,
            'amount_disbursed' => 1000,
            'disbursement_date' => Carbon::today()->subMonth(1),
            'interest_rate' => 10,
            'tenure' => 5,
            'user_id' => $user->id
        ]);

        $transaction = Transaction::create([
           'transaction_type' => TransactionType::LOAN_REPAYMENT,
            'transaction_date' => Carbon::today()->subMonths(1),
           'transaction_status' => TransactionStatus::COMPLETED,
           'transaction_amount' => 100,
           'transaction_medium' => TransactionMedium::ONLINE,
           'transaction_purpose' => 'TESTING',
            'owner_type' => 'LOAN',
            'owner_id' => $loanOne->id
        ]);

        $this->assertEqualsWithDelta(Carbon::today()->addMonths(1)->format('Y/M/d'), $loanOne->nextDuePaymentDate->format('Y/M/d'), 100);
        $this->assertEqualsWithDelta(Carbon::today()->format('Y/M/d'), $loanTwo->nextDuePaymentDate->format('Y/M/d'), 5);
    }
}
