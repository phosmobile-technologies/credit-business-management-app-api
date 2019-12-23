<?php

namespace App\Repositories\Interfaces;

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
}
