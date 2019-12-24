<?php

namespace Tests\GraphQL\Helpers\Traits;


use App\Models\Enums\LoanApplicationStatus;
use App\Models\Loan;

trait InteractsWIthTestLoans
{
    use InteractsWithTestUsers;

    /**
     * Create a test loan.
     *
     * @return Loan
     */
    public function createTestLoan(): Loan
    {
        $user = $this->createUser();
        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::PENDING(),
            'user_id' => $user->id
        ]);

        return $loan;
    }
}
