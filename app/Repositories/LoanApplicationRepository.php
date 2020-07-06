<?php
/**
 * Created by PhpStorm.
 * User: abraham
 * Date: 25/12/2019
 * Time: 4:32 PM
 */

namespace App\Repositories;


use App\Models\Loan;
use App\Models\LoanApplication;
use App\Repositories\Interfaces\LoanApplicationRepositoryInterface;

class LoanApplicationRepository implements LoanApplicationRepositoryInterface
{

    /**
     * Create a LoanApplication entry in the database.
     *
     * @param array $loanApplicationData
     * @return LoanApplication
     */
    public function create(array $loanApplicationData): LoanApplication
    {
        $loanApplication = LoanApplication::create($loanApplicationData);

        return $loanApplication;
    }

    /**
     * Assign an online loan application to an admin staff for handling
     *
     * @param string $loan_application_id
     * @param string $admin_staff_id
     * @param string $branch_manager_id
     * @return LoanApplication
     */
    public function assign(string $loan_application_id, string $admin_staff_id, string $branch_manager_id): LoanApplication
    {
        $loanApplication = $this->find($loan_application_id);

        $loanApplication->assignee_id = $admin_staff_id;
        $loanApplication->assigned_by = $branch_manager_id;

        $loanApplication->save();
        return $loanApplication;
    }

    /**
     * Process an online loan application.
     *
     * @param string $loan_application_id
     * @param string $new_status
     * @param string $message
     * @return LoanApplication
     */
    public function process(string $loan_application_id, string $new_status, string $message = ''): LoanApplication
    {
        $loanApplication = $this->find($loan_application_id);

        $loanApplication->status = $new_status;
        $loanApplication->processing_message = $message;

        $loanApplication->save();
        return $loanApplication;
    }

    /**
     * Find a loan application by id.
     *
     * @param string $loan_application_id
     * @return Loan|null
     */
    public function find(string $loan_application_id): ?LoanApplication
    {
        return LoanApplication::findOrFail($loan_application_id);
    }
}
