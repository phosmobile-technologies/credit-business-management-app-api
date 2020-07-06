<?php

namespace App\Services;


use App\Models\LoanApplication;
use App\Repositories\Interfaces\LoanApplicationRepositoryInterface;

class LoanApplicationService
{
    /**
     * @var LoanApplicationRepositoryInterface
     */
    private $loanApplicationRepository;

    public function __construct(LoanApplicationRepositoryInterface $loanApplicationRepository)
    {
        $this->loanApplicationRepository = $loanApplicationRepository;
    }

    /**
     * Create a loan application.
     *
     * @param array $loanApplicationData
     * @return \App\Models\LoanApplication
     */
    public function createLoanApplication(array $loanApplicationData)
    {
        return $this->loanApplicationRepository->create($loanApplicationData);
    }

    /**
     * @param string $loan_application_id
     * @param string $admin_staff_id
     * @param string $branch_manager_id
     * @return \App\Models\LoanApplication
     */
    public function assignLoanApplicationToAdminStaff(string $loan_application_id, string $admin_staff_id, string $branch_manager_id): LoanApplication
    {
        return $this->loanApplicationRepository->assign($loan_application_id, $admin_staff_id, $branch_manager_id);
    }

    /**
     * @param string      $loan_application_id
     * @param string      $status
     * @param null|string $message
     * @return LoanApplication
     */
    public function processLoanApplication(string $loan_application_id, string $status, ?string $message): LoanApplication
    {
        return $this->loanApplicationRepository->process($loan_application_id, $status, $message);
    }
}
