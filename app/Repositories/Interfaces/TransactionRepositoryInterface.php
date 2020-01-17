<?php

namespace App\Repositories\Interfaces;


use App\Models\ProcessedTransaction;
use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * Find a transaction by ID.
     *
     * @param string $transaction_id
     * @return Transaction
     */
    public function find(string $transaction_id): ?Transaction;

    /**
     * Create a transaction in the database
     *
     * @param array $transactionDetails
     * @return Transaction
     */
    public function create(array $transactionDetails): Transaction;

    /**
     * Update the transaction status of a transaction
     *
     * @param Transaction $transaction
     * @param string $status
     * @return Transaction
     */
    public function updateTransactionStatus(Transaction $transaction, string $status): Transaction;

    /**
     * Store a processed transaction for record keeping.
     *
     * @param Transaction $transaction
     * @param string $processor_id
     * @param string $action
     * @param null|string $message
     * @return ProcessedTransaction
     */
    public function storeProcessedTransaction(Transaction $transaction, string $processor_id, string $action, ?string $message): ProcessedTransaction;
}
