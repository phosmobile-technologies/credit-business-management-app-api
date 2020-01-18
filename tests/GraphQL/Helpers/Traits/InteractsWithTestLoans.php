<?php

namespace Tests\GraphQL\Helpers\Traits;


use App\Models\Enums\LoanApplicationStatus;
use App\Models\Loan;

trait InteractsWithTestLoans
{
    use InteractsWithTestUsers;

    /**
     * Create a test loan.
     *
     * @param null $user
     * @param null $numberOfLoans
     * @return mixed
     */
    public function createTestLoan($user = null, $numberOfLoans = null)
    {
        if (!$user) {
            $user = $this->createUser();
        }

        if (!$numberOfLoans) {
            return factory(Loan::class)->create([
                'application_status' => LoanApplicationStatus::PENDING(),
                'user_id' => $user->id
            ]);
        }

        return factory(Loan::class, $numberOfLoans)->create([
            'application_status' => LoanApplicationStatus::PENDING(),
            'user_id' => $user->id
        ]);
    }
}
