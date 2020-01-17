<?php

namespace App\Services;


use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\Loan;
use App\Models\Transaction;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\DB;

class LoanRepaymentTransactionService
{
    /**
     * @var LoanRepositoryInterface
     */
    private $loanRepository;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    public function __construct(LoanRepositoryInterface $loanRepository, TransactionRepositoryInterface $transactionRepository)
    {
        $this->loanRepository = $loanRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Create a loan repayment transaction.
     *
     * @param Loan $loan
     * @param array $transactionDetails
     * @return \App\Models\Transaction
     */
    public function initiateLoanRepaymentTransaction(Loan $loan, array $transactionDetails)
    {
        $transactionData = [
            'owner_id' => $loan->id,
            'owner_type' => TransactionOwnerType::LOAN,
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
     * @param User $user The user processing the transaction
     * @param string $loan_id
     * @param string $transaction_id
     * @param string $action
     * @param null|string $message
     * @return Transaction|null
     */
    public function processLoanRepaymentTransaction(User $user, string $loan_id, string $transaction_id, string $action, ?string $message)
    {
        $loan = $this->loanRepository->find($loan_id);
        $transaction = $this->transactionRepository->find($transaction_id);

        DB::transaction(function () use ($loan, $transaction, $action, $user, $message) {
            switch ($action) {
                case TransactionProcessingActions::APPROVE:
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
