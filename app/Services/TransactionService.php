<?php

namespace App\Services;


use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Models\Transaction;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var ContributionRepositoryInterface
     */
    private $contributionRepository;
    /**
     * @var LoanRepositoryInterface
     */
    private $loanRepository;

    /**
     * TransactionService constructor.
     *
     * @param TransactionRepositoryInterface $transactionRepository
     * @param ContributionRepositoryInterface $contributionRepository
     * @param LoanRepositoryInterface $loanRepository
     */
    public function __construct(TransactionRepositoryInterface $transactionRepository, ContributionRepositoryInterface $contributionRepository, LoanRepositoryInterface $loanRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->contributionRepository = $contributionRepository;
        $this->loanRepository = $loanRepository;
    }

    public function initiateTransaction(string $owner_id, array $transactionDetails)
    {
        switch ($transactionDetails['transaction_type']) {
            case TransactionType::LOAN_REPAYMENT:
                return $this->initiateLoanRepaymentTransaction($owner_id, $transactionDetails);
                break;

            case TransactionType::CONTRIBUTION_PAYMENT:
                return $this->initiateContributionPlanPaymentTransaction($owner_id, $transactionDetails);
                break;

            case TransactionType::CONTRIBUTION_WITHDRAWAL:
                return $this->initiateContributionPlanWithdrawalTransaction($owner_id, $transactionDetails);
                break;
        }
    }

    /**
     * Process (approve or disapprove) a transaction.
     *
     * @param User $user
     * @param string $transaction_id
     * @param string $action
     * @param null|string $message
     * @return Transaction
     */
    public function processTransaction(User $user, string $transaction_id, string $action, ?string $message): Transaction
    {
        $transaction = $this->transactionRepository->find($transaction_id);

        switch ($transaction->transaction_type) {
            case (TransactionType::CONTRIBUTION_PAYMENT):
                return $this->processContributionPaymentTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::CONTRIBUTION_WITHDRAWAL):
                return $this->processContributionWithdrawalTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::LOAN_REPAYMENT):
                return $this->processLoanRepaymentTransaction($user, $transaction, $action, $message);
                break;
        }
    }

    /**
     * Initiate a contribution plan payment transaction.
     *
     * @param string $contribution_plan_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateContributionPlanPaymentTransaction(string $contribution_plan_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::CONTRIBUTION_PLAN, $contribution_plan_id, $transactionDetails);
    }

    /**
     * Initiate a contribution plan withdrawal transaction.
     *
     * @param string $contribution_plan_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateContributionPlanWithdrawalTransaction(string $contribution_plan_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::CONTRIBUTION_PLAN, $contribution_plan_id, $transactionDetails);
    }

    /**
     * Create a loan repayment transaction.
     *
     * @param string $loan_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateLoanRepaymentTransaction(string $loan_id, array $transactionDetails)
    {
        return $this->createTransaction(TransactionOwnerType::LOAN, $loan_id, $transactionDetails);
    }

    /**
     * Create a new transaction.
     *
     * @param string $transactionOwnerType
     * @param string $ownerId
     * @param array $transactionDetails
     * @return \App\Models\Transaction
     */
    private function createTransaction(string $transactionOwnerType, string $ownerId, array $transactionDetails)
    {
        $transactionData = [
            'owner_id' => $ownerId,
            'owner_type' => $transactionOwnerType,
            'transaction_date' => $transactionDetails['transaction_date'],
            'transaction_type' => $transactionDetails['transaction_type'],
            'transaction_amount' => $transactionDetails['transaction_amount'],
            'transaction_medium' => $transactionDetails['transaction_medium'],
            'transaction_purpose' => $transactionDetails['transaction_purpose'],
            'transaction_status' => TransactionStatus::PENDING
        ];

        $transaction = $this->transactionRepository->create($transactionData);

        return $transaction;
    }

    /**
     * Process (approve or disapprove) a contribution payment transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     * @param string $action
     * @param null|string $message
     * @return Transaction
     */
    private function processContributionPaymentTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $contributionPlan = $this->contributionRepository->find($transaction->owner_id);
                    $this->contributionRepository->addPayment($contributionPlan, $transaction);
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
        });

        return $transaction;
    }

    /**
     * Process (approve or disapprove) a contribution withdrawal transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     * @param string $action
     * @param null|string $message
     * @return Transaction
     */
    private function processContributionWithdrawalTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $contributionPlan = $this->contributionRepository->find($transaction->owner_id);
                    $this->contributionRepository->withdraw($contributionPlan, $transaction);
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
        });

        return $transaction;
    }

    /**
     * @param User $user The user processing the transaction
     * @param Transaction $transaction
     * @param string $action
     * @param null|string $message
     * @return Transaction|null
     */
    private function processLoanRepaymentTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $loan = $this->loanRepository->find($transaction->owner_id);
                    $this->loanRepository->repayLoan($loan, $transaction->transaction_amount);
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
        });

        return $transaction;
    }
}
