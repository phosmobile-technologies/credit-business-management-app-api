<?php

namespace App\Repositories;


use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{

    /**
     * Find a transaction by ID.
     *
     * @param string $transaction_id
     * @return Transaction
     */
    public function find(string $transaction_id): Transaction
    {
        return Transaction::findOrFail($transaction_id);
    }

    /**
     * Create a transaction in the database
     *
     * @param array $transactionDetails
     * @return Transaction
     */
    public function create(array $transactionDetails): Transaction
    {
        return Transaction::create($transactionDetails);
    }

    /**
     * Update the transaction status of a transaction
     *
     * @param Transaction $transaction
     * @param string $status
     * @return Transaction
     */
    public function updateTransactionStatus(Transaction $transaction, string $status): Transaction
    {
        $transaction->transaction_status = $status;

        $transaction->save();

        return $transaction;
    }

    /**
     * Store a processed transaction for record keeping.
     *
     * @param Transaction $transaction
     * @param string $processor_id
     * @param string $action
     * @param null|string $message
     * @return ProcessedTransaction
     */
    public function storeProcessedTransaction(Transaction $transaction, string $processor_id, string $action, ?string $message): ProcessedTransaction
    {
        return ProcessedTransaction::create([
            'causer_id' => $processor_id,
            'transaction_id' => $transaction->id,
            'processing_type' => $action,
            'message' => $message
        ]);
    }
}
