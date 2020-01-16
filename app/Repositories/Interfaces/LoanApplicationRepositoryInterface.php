<?php

namespace App\Repositories\Interfaces;


use App\Models\Loan;
use App\Models\LoanApplication;

interface LoanApplicationRepositoryInterface
{
    /**
     * Create a LoanApplication entry in the database.
     *
     * @param array $loanApplicationData
     * @return LoanApplication
     */
    public function create(array $loanApplicationData): LoanApplication;
}
