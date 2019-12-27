<?php

namespace App\Services;


use App\Events\LoanApprovedByBranchManager;
use App\Events\LoanApprovedByGlobalManager;
use App\Events\LoanDisApprovedByBranchManager;
use App\Events\LoanDisbursed;
use App\Events\NewLoanCreated;
use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanService
{
    /**
     * @var LoanRepositoryInterface
     */
    private $loanRepository;

    /**
     * LoanService constructor.
     * @param LoanRepositoryInterface $loanRepository
     */
    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    /**
     * Create a new loan.
     *
     * @param array $loanData
     * @return Loan
     */
    public function create(array $loanData): Loan
    {
        // We need to generate a unique (app specified) identifier for each loan
        $loanData['loan_identifier'] = $this->generateLoanIdentifier();

        // Ensure that the default values when creating a loan are set
        $loanData['disbursement_status'] = DisbursementStatus::NOT_DISBURSED;
        $loanData['application_status'] = LoanApplicationStatus::PENDING;
        $loanData['loan_condition_status'] = LoanConditionStatus::INACTIVE;
        $loanData['loan_default_status'] = LoanDefaultStatus::NOT_DEFAULTING;

        $loan = $this->loanRepository->create($loanData);

        event(new NewLoanCreated($loan));

        return $loan;
    }

    /**
     * Generate a random loan identifier for a loan.
     *
     * @return int
     */
    private function generateLoanIdentifier(): int
    {
        $identifier = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the loan_identifier exists already
        if ($this->loanRepository->loanIdentifierExists($identifier)) {
            return self::generateLoanIdentifier();
        }

        return $identifier;
    }

    /**
     * Update the application_state of a loan.
     *
     * @param string $loanID
     * @param string $loanApplicationStatus
     * @param null|string $message
     * @return Loan
     */
    public function updateLoanApplicationStatus(string $loanID, string $loanApplicationStatus, ?string $message)
    {
        $loan = Loan::where('id', $loanID)->firstOrFail();

        $this->loanRepository->updateApplicationState($loan, $loanApplicationStatus);

        switch ($loanApplicationStatus) {
            case LoanApplicationStatus::APPROVED_BY_BRANCH_MANAGER():
                event(new LoanApprovedByBranchManager($loan, $message));
                break;
            case LoanApplicationStatus::DISAPPROVED_BY_BRANCH_MANAGER():
                event(new LoanDisApprovedByBranchManager($loan, $message));
                break;
            case LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER():
                event(new LoanApprovedByGlobalManager($loan, $message));
                break;
            case LoanApplicationStatus::DISAPPROVED_BY_GLOBAL_MANAGER():
                event(new LoanDisApprovedByBranchManager($loan, $message));
                break;
        }

        return $loan;
    }

    /**
     * Disburse a loan
     *
     * @param string $loanID
     * @param float $amountDisbursed
     * @param null|string $message
     * @return
     * @throws \Exception
     */
    public function disburseLoan(string $loanID, float $amountDisbursed, ?string $message) {
        $loan = Loan::where('id', $loanID)->firstOrFail();
        $loanBalance = $loan->loan_balance;

        if($loanBalance < $amountDisbursed) {
            throw new GraphqlError("Cannot disburse {$amountDisbursed}, the loan balance is only {$loanBalance}");
        }

        $this->loanRepository->disburseLoan($loan, $amountDisbursed);

        event(new LoanDisbursed($loan, $amountDisbursed, $message));

        return $loan;
    }

}
