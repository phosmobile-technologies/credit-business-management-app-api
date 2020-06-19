<?php

namespace Tests\Unit\Models;

use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\LoanConditionStatus;
use Carbon\Carbon;
use Tests\TestCase;

use App\Models\Loan;

class LoanModelTest extends TestCase
{
    /**
     * @test
     */
    public function test_it_correctly_gets_number_of_months_left_for_a_loan() {
        $loanOne = factory(Loan::class)->make([
            'due_date' => null,
            'loan_condition_status' => LoanConditionStatus::INACTIVE
        ]);

        $loanTwo = factory(Loan::class)->make([
            'due_date' => null,
            'loan_condition_status' => LoanConditionStatus::COMPLETED
        ]);

        $loanThree = factory(Loan::class)->make([
            'due_date' => Carbon::today()->addMonths(24),
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $this->assertEquals(0, $loanOne->monthsLeft);
        $this->assertEquals(0, $loanTwo->monthsLeft);
        $this->assertEquals(24, $loanThree->monthsLeft);
    }

    /**
     * @test
     */
    public function testItThrowsErrorWhenGettingDueDateForLoanWithoutDueDateAndTaggedAsActiveOrNonPerforming() {
        $this->expectException(GraphqlError::class);

        $loanOne = factory(Loan::class)->make([
            'due_date' => null,
            'loan_condition_status' => LoanConditionStatus::ACTIVE
        ]);

        $numberOfMonthsLeft = $loanOne->monthsLeft;
    }

    /**
     * @test
     */
    public function testItCorrectlyGetsNextDueAmount() {
        $loanOne = factory(Loan::class)->make([
            'due_date' => Carbon::today()->addMonths(5),
            'loan_condition_status' => LoanConditionStatus::ACTIVE,
            'amount_disbursed' => 1000,
            'interest_rate' => 10,
            'tenure' => 5
        ]);

        $this->assertEquals(300, $loanOne->totalInterestAmount);
    }

}
