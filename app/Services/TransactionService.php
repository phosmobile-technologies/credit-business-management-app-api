<?php

namespace App\Services;


use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
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
     * TransactionService constructor.
     *
     * @param TransactionRepositoryInterface $transactionRepository
     * @param ContributionRepositoryInterface $contributionRepository
     */
    public function __construct(TransactionRepositoryInterface $transactionRepository, ContributionRepositoryInterface $contributionRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->contributionRepository = $contributionRepository;
    }

    /**
     * Initiate a contribution plan transaction.
     *
     * @param string $contribution_plan_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateContributionPlanTransaction(string $contribution_plan_id, array $transactionDetails): Transaction
    {
        return $this->createTransaction(TransactionOwnerType::CONTRIBUTION_PLAN, $contribution_plan_id, $transactionDetails);
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
        }
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
        $contributionPlan = $this->contributionRepository->find($transaction->owner_id);

        DB::transaction(function () use ($contributionPlan, $transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
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
}
