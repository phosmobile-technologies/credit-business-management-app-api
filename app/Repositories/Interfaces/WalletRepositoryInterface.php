<?php

namespace App\Repositories\Interfaces;


use App\Models\Wallet;
use App\Models\Transaction;

/**
 * Interface WalletRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface WalletRepositoryInterface
{
    /**
     * Create a wallet in the database.
     *
     * @param array $walletData
     * @return Wallet
     */
    public function create(array $walletData): Wallet;

    /**
     * Update a wallet in the database.
     *
     * @param string $id
     * @param array $walletData
     * @return Wallet
     */
    public function update(string $id, array $walletData): Wallet;

    /**
     * Find a wallet by id.
     *
     * @param string $wallet_id
     * @return Wallet|null
     */
    public function find(string $wallet_id): ?Wallet;

    /**
     * Add a payment to a wallet plan.
     *
     * @param Wallet $wallet
     * @param Transaction $transaction
     * @return Wallet
     */
    public function addPayment(Wallet $wallet, Transaction $transaction): Wallet;

    /**
     * Withdraw funds from a wallet plan.
     *
     * @param Wallet $wallet
     * @param Transaction $transaction
     * @return Wallet
     */
    public function withdraw(Wallet $wallet, Transaction $transaction): Wallet;
}
