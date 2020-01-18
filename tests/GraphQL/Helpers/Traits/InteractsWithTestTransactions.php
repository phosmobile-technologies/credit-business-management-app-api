<?php

namespace Tests\GraphQL\Helpers\Traits;

use App\Models\enums\TransactionStatus;
use App\Models\Transaction;

trait InteractsWithTestTransactions
{
    /**
     * Create a test Transaction.
     *
     * @param string $owner_type
     * @param string $owner_id
     * @return mixed
     */
    public function createTransaction(string $owner_type, string $owner_id)
    {
        $transaction = $transaction = factory(Transaction::class)->create([
            'transaction_status' => TransactionStatus::PENDING,
            'owner_type' => $owner_type,
            'owner_id' => $owner_id
        ]);

        return $transaction;
    }
}
