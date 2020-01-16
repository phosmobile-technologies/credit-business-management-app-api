<?php

namespace App\Repositories\Interfaces;


use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * Find a transaction by ID.
     *
     * @param string $transaction_id
     * @return Transaction
     */
    public function find (string $transaction_id): ?Transaction;

    /**
     * Create a transaction in the database
     *
     * @param array $transactionDetails
     * @return Transaction
     */
    public function create(array $transactionDetails): Transaction;
}
