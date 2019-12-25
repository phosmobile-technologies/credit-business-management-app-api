<?php

namespace App\Services;


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
    public function createLoanApplication(array $loanApplicationData) {
        return $this->loanApplicationRepository->create($loanApplicationData);
    }
}
