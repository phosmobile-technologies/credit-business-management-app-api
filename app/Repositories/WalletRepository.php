<?php

namespace App\Repositories;


use App\GraphQL\Errors\GraphqlError;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletRepository implements WalletRepositoryInterface
{

    /**
     * Insert a Wallet in the database.
     *
     * @param array $walletData
     * @return Wallet
     */
    public function create(array $walletData): Wallet
    {
        return Wallet::create($walletData);
    }

    /**
     * Update a Wallet in the database.
     *
     * @param string $id
     * @param array $walletData
     * @return Wallet
     */
    public function update(string $id, array $walletData): Wallet
    {
        $wallet = $this->find($id);

        $wallet->update($walletData);
        return $wallet;
    }

    /**
     * Find a Wallet by id.
     *
     * @param string $wallet_id
     * @return Wallet|null
     */
    public function find(string $wallet_id): Wallet
    {
        $wallet = Wallet::findOrFail($wallet_id);
        return $wallet;
    }

    /**
     * Add a payment to a Wallet.
     *
     * @param Wallet $wallet
     * @param Transaction $transaction
     * @return Wallet
     */
    public function addPayment(Wallet $wallet, Transaction $transaction): Wallet
    {
        $wallet->wallet_balance = $wallet->wallet_balance + $transaction->transaction_amount;
        $wallet->save();

        return $wallet;
    }

    /**
     * Withdraw funds from a Wallet plan.
     *
     * @param Wallet $wallet
     * @param Transaction $transaction
     * @return Wallet
     * @throws GraphqlError
     */
    public function withdraw(Wallet $wallet, Transaction $transaction): Wallet
    {
        if ($transaction->transaction_amount > $wallet->wallet_balance) {
            throw new GraphqlError("Insufficient Wallet balance to make this withdrawal");
        }

        $wallet->wallet_balance = $wallet->wallet_balance - $transaction->transaction_amount;
        $wallet->save();

        return $wallet;
    }
}