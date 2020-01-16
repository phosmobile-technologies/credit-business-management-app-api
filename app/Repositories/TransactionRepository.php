<?php

namespace App\Repositories;


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
}
