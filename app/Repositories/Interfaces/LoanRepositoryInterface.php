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

    /**
     * Find a loan by id.
     *
     * @param string $loan_id
     * @return Loan|null
     */
    public function find(string $loan_id): ?Loan;

    /**
     * Repay a loan
     *
     * @param Loan $loan
     * @param float $repayment_amount
     * @throws GraphqlError
     * @return Loan
     */
    public function repayLoan(Loan $loan, float $repayment_amount): Loan;

    /**
     * Assign a loan to an admin staff for handling
     *
     * @param string $loan_id
     * @param string $admin_staff_id
     * @param string $branch_manager_id
     * @return Loan
     */
    public function assign(string $loan_id, string $admin_staff_id, string $branch_manager_id): Loan;
}
