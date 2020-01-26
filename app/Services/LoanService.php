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
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanService
{
    /**
     * @var LoanRepositoryInterface
     */
    private $loanRepository;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * LoanService constructor.
     *
     * @param LoanRepositoryInterface $loanRepository
     * @param TransactionService $transactionService
     */
    public function __construct(LoanRepositoryInterface $loanRepository, TransactionService $transactionService)
    {
        $this->loanRepository = $loanRepository;
        $this->transactionService = $transactionService;
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
        $loan = $this->loanRepository->find($loanID);

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
        $loan = $this->loanRepository->find($loanID);
        $loanBalance = $loan->loan_balance;

        if($loanBalance < $amountDisbursed) {
            throw new GraphqlError("Cannot disburse {$amountDisbursed}, the loan balance is only {$loanBalance}");
        }

        if($loan->application_status !== LoanApplicationStatus::APPROVED_BY_GLOBAL_MANAGER()->getValue()) {
            throw new GraphqlError("Cannot disburse funds for a loan that is not approved");
        }

        $this->loanRepository->disburseLoan($loan, $amountDisbursed);

        event(new LoanDisbursed($loan, $amountDisbursed, $message));

        return $loan;
    }

    /**
     * Repay a loan
     *
     * @param string $loan_id
     * @param array $transactionDetails
     * @return \App\Models\Transaction
     * @throws GraphqlError
     */
    public function initiateLoanRepayment(string $loan_id, array $transactionDetails) {
        $loan = $this->loanRepository->find($loan_id);

        $transactionAmount = $transactionDetails['transaction_amount'];

        if($transactionDetails['transaction_amount'] > $loan->loan_balance) {
            throw new GraphqlError("Transaction amount {$transactionAmount} is greater than the total loan balance");
        }

        if($transactionDetails['transaction_type'] !== TransactionType::LOAN_REPAYMENT) {
            throw new GraphqlError("The transaction type selected must be Loan Repayment");
        }

        $transaction = $this->transactionService->initiateLoanRepaymentTransaction($loan, $transactionDetails);

        return $transaction;
    }

}
