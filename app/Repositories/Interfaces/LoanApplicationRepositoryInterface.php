<?php

namespace App\Repositories\Interfaces;


use App\Models\enums\OnlineLoanApplicationStatus;
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

    /**
     * Find a loan application by id.
     *
     * @param string $loan_application_id
     * @return Loan|null
     */
    public function find(string $loan_application_id): ?LoanApplication;

    /**
     * Assign an online loan application to an admin staff for handling
     *
     * @param string $loan_application_id
     * @param string $admin_staff_id
     * @param string $branch_manager_id
     * @return LoanApplication
     */
    public function assign(string $loan_application_id, string $admin_staff_id, string $branch_manager_id): LoanApplication;

    /**
     * Process an online loan application.
     *
     * @param string $loan_application_id
     * @param string $new_status
     * @param string $message
     * @return LoanApplication
     */
    public function process(string $loan_application_id, string $new_status, string $message = ''): LoanApplication;
}
