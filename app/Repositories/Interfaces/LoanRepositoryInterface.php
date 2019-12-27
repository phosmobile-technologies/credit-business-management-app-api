<?php

namespace App\Repositories\Interfaces;

use App\Models\Enums\LoanApplicationStatus;
use App\Models\Loan;

interface LoanRepositoryInterface {

    /**
     * Insert a loan in the database
     *
     * @param array $loanData
     * @return Loan
     */
    public function create(array $loanData): Loan;

    /**
     * Determine if a loan with the loan_identifier exists.
     *
     * @param int $identifier
     * @return bool
     */
    public function loanIdentifierExists(int $identifier): bool;

    /**
     * Update the application_state of a loan in the database.
     *
     * @param Loan $loan
     * @param string $applicationState
     * @return Loan
     */
    public function updateApplicationState(Loan $loan, string $applicationState): Loan;

    /**
     * Store a loan disbursement action in the database.
     *
     * @param Loan $loan
     * @param float $amountDisbursed
     * @return Loan
     */
    public function disburseLoan(Loan $loan, float $amountDisbursed): Loan;
}
