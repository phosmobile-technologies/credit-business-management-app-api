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
     * @param null $user
     * @return Loan
     */
    public function createTestLoan($user = null): Loan
    {
        if(!$user) {
            $user = $this->createUser();
        }

        $loan = factory(Loan::class)->create([
            'application_status' => LoanApplicationStatus::PENDING(),
            'user_id' => $user->id
        ]);

        return $loan;
    }
}
