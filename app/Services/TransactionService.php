<?php

namespace App\Services;


use App\Events\TransactionProcessedEvent;
use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\LoanApplicationStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\enums\TransactionMedium;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Loan;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\UserRepository;
use App\User;
use Carbon\Carbon;
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
     * @var WalletRepositoryInterface
     */
    private $walletRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * TransactionService constructor.
     *
     * @param TransactionRepositoryInterface  $transactionRepository
     * @param ContributionRepositoryInterface $contributionRepository
     * @param LoanRepositoryInterface         $loanRepository
     * @param WalletRepositoryInterface       $walletRepository
     * @param UserRepository                  $userRepository
     */
    public function __construct(TransactionRepositoryInterface $transactionRepository, ContributionRepositoryInterface $contributionRepository,
                                LoanRepositoryInterface $loanRepository,
                                WalletRepositoryInterface $walletRepository,
                                UserRepository $userRepository)
    {
        $this->transactionRepository  = $transactionRepository;
        $this->contributionRepository = $contributionRepository;
        $this->loanRepository         = $loanRepository;
        $this->walletRepository       = $walletRepository;
        $this->userRepository         = $userRepository;
    }

    /**
     * @param string $owner_id
     * @param array  $transactionDetails
     * @return Transaction
     * @throws GraphqlError
     */
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

            case TransactionType::WALLET_PAYMENT:
                return $this->initiateWalletPaymentTransaction($owner_id, $transactionDetails);
                break;

            case TransactionType::WALLET_WITHDRAWAL:
                return $this->initiateWalletWithdrawalTransaction($owner_id, $transactionDetails);
                break;

            case TransactionType::BRANCH_FUND_DISBURSEMENT:
                return $this->initiateBranchFundDisbursementTransaction($owner_id, $transactionDetails);
                break;

            case TransactionType::BRANCH_EXPENSE:
                return $this->initiateBranchExpenseTransaction($owner_id, $transactionDetails);
                break;
        }
    }

    /**
     * Process (approve or disapprove) a transaction.
     *
     * @param User        $user
     * @param string      $transaction_id
     * @param string      $action
     * @param null|string $message
     * @return Transaction|array
     */
    public function processTransaction(User $user, string $transaction_id, string $action, ?string $message)
    {
        $transaction = $this->transactionRepository->find($transaction_id);

        switch ($transaction->transaction_type) {
            case (TransactionType::CONTRIBUTION_PAYMENT):
                return $this->processContributionPaymentTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::CONTRIBUTION_WITHDRAWAL):
                return $this->processContributionWithdrawalTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::WALLET_PAYMENT):
                return $this->processWalletPaymentTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::WALLET_WITHDRAWAL):
                return $this->processWalletWithdrawalTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::LOAN_REPAYMENT):
                return $this->processLoanRepaymentTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::BRANCH_FUND_DISBURSEMENT):
                return $this->processBranchFundReimbursementTransaction($user, $transaction, $action, $message);
                break;

            case (TransactionType::BRANCH_EXPENSE):
                return $this->processBranchFundExpenseTransaction($user, $transaction, $action, $message);
                break;
        }
    }

    /**
     * Initiate a contribution plan payment transaction.
     *
     * @param string $contribution_plan_id
     * @param array  $transactionDetails
     * @return Transaction
     */
    public function initiateContributionPlanPaymentTransaction(string $contribution_plan_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::CONTRIBUTION_PLAN, $contribution_plan_id, $transactionDetails);
    }

    /**
     * Initiate a contribution plan payment transaction.
     *
     * @param string $contribution_plan_id
     * @param array  $transactionDetails
     * @return Transaction
     */
    public function initiateContributionPlanWithdrawalTransaction(string $contribution_plan_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::CONTRIBUTION_PLAN, $contribution_plan_id, $transactionDetails);
    }

    /**
     * Initiate a wallet payment transaction.
     *
     * @param string $wallet_id
     * @param array  $transactionDetails
     * @return Transaction
     */
    public function initiateWalletPaymentTransaction(string $wallet_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::WALLET, $wallet_id, $transactionDetails);
    }

    /**
     * Initiate a wallet withdrawal transaction.
     *
     * @param string $wallet_id
     * @param array  $transactionDetails
     * @return Transaction
     */
    public function initiateWalletWithdrawalTransaction(string $wallet_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::WALLET, $wallet_id, $transactionDetails);
    }

    /**
     * Create a loan repayment transaction.
     *
     * @param string $loan_id
     * @param array  $transactionDetails
     * @return Transaction
     * @throws GraphqlError
     */
    public function initiateLoanRepaymentTransaction(string $loan_id, array $transactionDetails)
    {
        $loan = $this->loanRepository->find($loan_id);
        if ($loan->loan_condition_status !== LoanConditionStatus::ACTIVE && $loan->loan_condition_status !== LoanConditionStatus::NONPERFORMING) {
            throw new GraphqlError('Cannot repay a loan that is inactive or completed');
        }

        return $this->createTransaction(TransactionOwnerType::LOAN, $loan_id, $transactionDetails);
    }

    /**
     * Create a branch fund disbursement transaction.
     *
     * @param string $branch_id
     * @param array  $transactionDetails
     * @return Transaction
     */
    public function initiateBranchFundDisbursementTransaction(string $branch_id, array $transactionDetails)
    {
        return $this->createTransaction(TransactionOwnerType::COMPANY_BRANCH, $branch_id, $transactionDetails);
    }

    /**
     * Create a branch fund disbursement transaction.
     *
     * @param string $branch_id
     * @param array  $transactionDetails
     * @return Transaction
     */
    public function initiateBranchExpenseTransaction(string $branch_id, array $transactionDetails)
    {
        return $this->createTransaction(TransactionOwnerType::COMPANY_BRANCH, $branch_id, $transactionDetails);
    }

    /**
     * Withdraw from a user's wallet and use it to repay a loan
     *
     * @param string $loan_id
     * @param string $wallet_id
     * @param float  $amount
     * @param string $user_id
     * @return Transaction|null
     */
    public function makeLoanRepaymentFromWallet(string $loan_id, string $wallet_id, float $amount, string $user_id)
    {
        $loan                     = $this->loanRepository->find($loan_id);
        $wallet                   = $this->walletRepository->find($wallet_id);
        $user                     = $this->userRepository->find($user_id);
        $loanRepaymentTransaction = null;

        DB::transaction(function () use ($loan, $wallet, $user, $amount, &$loanRepaymentTransaction) {
            $walletWithdrawalTransaction = $this->initiateTransaction($wallet->id, [
                'transaction_date'    => Carbon::now()->toDateString(),
                'transaction_type'    => TransactionType::WALLET_WITHDRAWAL,
                'transaction_amount'  => $amount,
                'transaction_medium'  => TransactionMedium::ONLINE,
                'transaction_purpose' => "Online withdrawing from user wallet for the purpose of repaying a loan",
                'branch_id'           => $user->profile->branch->id
            ]);

            $walletWithdrawalTransaction = $this->processTransaction($user, $walletWithdrawalTransaction->id,
                TransactionProcessingActions::APPROVE, "Approved as an online user wallet withdrawal transaction");

            $loanRepaymentTransaction = $this->initiateTransaction($loan->id, [
                'transaction_date'    => Carbon::now()->toDateString(),
                'transaction_type'    => TransactionType::LOAN_REPAYMENT,
                'transaction_amount'  => $amount,
                'transaction_medium'  => TransactionMedium::ONLINE,
                'transaction_purpose' => "Online Loan repayment from funds withdrawn for user's wallet",
                'branch_id'           => $user->profile->branch->id
            ]);

            $this->processTransaction($user, $loanRepaymentTransaction->id,
                TransactionProcessingActions::APPROVE, "Approved as an online user loan repayment from funds gotten from user wallet");

        });

        return $this->transactionRepository->find($loanRepaymentTransaction->id);
    }

    /**
     * Create a new transaction.
     *
     * @param string $transactionOwnerType
     * @param string $ownerId
     * @param array  $transactionDetails
     * @return \App\Models\Transaction
     */
    private function createTransaction(string $transactionOwnerType, string $ownerId, array $transactionDetails)
    {
        $transactionData = [
            'owner_id'            => $ownerId,
            'owner_type'          => $transactionOwnerType,
            'transaction_date'    => $transactionDetails['transaction_date'],
            'transaction_type'    => $transactionDetails['transaction_type'],
            'transaction_amount'  => $transactionDetails['transaction_amount'],
            'transaction_medium'  => $transactionDetails['transaction_medium'],
            'transaction_purpose' => $transactionDetails['transaction_purpose'],
            'branch_id'           => $transactionDetails['branch_id'],
            'transaction_status'  => TransactionStatus::PENDING
        ];

        $transaction = $this->transactionRepository->create($transactionData);

        return $transaction;
    }

    /**
     * Process (approve or disapprove) a contribution payment transaction.
     *
     * @param User        $user
     * @param Transaction $transaction
     * @param string      $action
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
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return $transaction;
    }

    /**
     * Process (approve or disapprove) a contribution withdrawal transaction.
     *
     * @param User        $user
     * @param Transaction $transaction
     * @param string      $action
     * @param null|string $message
     * @return array
     */
    private function processContributionWithdrawalTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        $amountSentToWallet = $transaction->transaction_amount;
        DB::transaction(function () use ($transaction, $action, $user, $message, &$amountSentToWallet) {

            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $contributionPlan = $this->contributionRepository->find($transaction->owner_id);
                    list ($contributionPlan, $amountSentToWallet) = $this->contributionRepository->withdraw($contributionPlan, $transaction);
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return [$transaction, $amountSentToWallet];
    }

    /**
     * Process (approve or disapprove) a wallet payment transaction.
     *
     * @param User        $user
     * @param Transaction $transaction
     * @param string      $action
     * @param null|string $message
     * @return Transaction
     */
    private function processWalletPaymentTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $wallet = $this->walletRepository->find($transaction->owner_id);
                    $this->walletRepository->addPayment($wallet, $transaction);
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return $transaction;
    }

    /**
     * Process (approve or disapprove) a wallet withdrawal transaction.
     *
     * @param User        $user
     * @param Transaction $transaction
     * @param string      $action
     * @param null|string $message
     * @return Transaction
     */
    private function processWalletWithdrawalTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $wallet = $this->walletRepository->find($transaction->owner_id);
                    $this->walletRepository->withdraw($wallet, $transaction);
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return $transaction;
    }

    /**
     * @param User        $user The user processing the transaction
     * @param Transaction $transaction
     * @param string      $action
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
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return $transaction;
    }

    /**
     * @param User        $user The user processing the transaction
     * @param Transaction $transaction
     * @param string      $action
     * @param null|string $message
     * @return Transaction|null
     */
    private function processBranchFundReimbursementTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return $transaction;
    }

    /**
     * @param User        $user The user processing the transaction
     * @param Transaction $transaction
     * @param string      $action
     * @param null|string $message
     * @return Transaction|null
     */
    private function processBranchFundExpenseTransaction(User $user, Transaction $transaction, string $action, ?string $message)
    {
        DB::transaction(function () use ($transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $transaction = $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $processedTransaction = $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $action, $message);
            event(new TransactionProcessedEvent($user, $transaction, $processedTransaction));
        });

        return $transaction;
    }
}
