<?php

namespace App\Services;


use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionService
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
}
