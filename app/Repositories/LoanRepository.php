<?php
/**
 * Created by PhpStorm.
 * User: abraham
 * Date: 23/12/2019
 * Time: 9:25 PM
 */

namespace App\Repositories;


use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanRepository implements LoanRepositoryInterface
{

    /**
     * Insert a loan in the database
     *
     * @param array $loanData
     * @return Loan
     */
    public function create(array $loanData): Loan
    {
        return Loan::create($loanData);
    }

    /**
     * Determine if a loan with the loan_identifier exists.
     *
     * @param int $identifier
     * @return bool
     */
    public function loanIdentifierExists(int $identifier): bool
    {
        return Loan::where('loan_identifier', $identifier)->exists();
    }

    /**
     * Update the application_state of a loan in the database.
     *
     * @param Loan $loan
     * @param string $applicationState
     * @return Loan
     */
    public function updateApplicationState(Loan $loan, string $applicationState): Loan
    {
        $loan->application_status = $applicationState;
        $loan->save();

        return $loan;
    }

    /**
     * Store a loan disbursement action in the database.
     *
     * @param Loan $loan
     * @param float $amountDisbursed
     * @return Loan
     */
    public function disburseLoan(Loan $loan, float $amountDisbursed): Loan
    {
        $loan->disbursement_status = DisbursementStatus::DISBURSED;
        $loan->loan_balance = $loan->loan_amount - ($loan->amount_disbursed + $amountDisbursed);
        $loan->amount_disbursed = $loan->amount_disbursed + $amountDisbursed;
        $loan->save();

        return $loan;
    }


    /**
     * Find a loan by id.
     *
     * @param string $loan_id
     * @return Loan|null
     */
    public function find(string $loan_id): ?Loan
    {
        return Loan::findOrFail($loan_id);
    }
}
