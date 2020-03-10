<?php

namespace App\Services;


use App\Models\Wallet;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Transaction;
use App\GraphQL\Errors\GraphqlError;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Class WalletService
 *
 * Service handling Wallets.
 *
 * @package App\Services
 */
class WalletService
{
    /**
     * @var WalletRepositoryInterface
     */
    private $walletRepository;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * WalletService constructor.
     * @param WalletRepositoryInterface $WalletRepository
     * @param TransactionService $transactionService
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(WalletRepositoryInterface $walletRepository, TransactionService $transactionService, UserRepositoryInterface $userRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->transactionService = $transactionService;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a Wallet
     *
     * @param array $walletData
     * @return Wallet
     * @throws GraphqlError
     */
    public function create(array $walletData): Wallet
    {
        // Check to ensure that a user can only have one active wallet at a time
        $user = $this->userRepository->find($walletData['user_id']);
        if (count($user->activeWallets()) > 0) {
            throw new GraphqlError('This user already has an active wallet and cannot take a new wallet');
        }

        return $this->walletRepository->create($walletData);
    }

    /**
     * Update a Wallet
     *
     * @param array $walletData
     * @return Wallet
     */
    public function update(array $walletData): Wallet
    {
        $walletData = collect($walletData);
        $id = $walletData['id'];
        $data = $walletData->except(['id'])->toArray();

        return $this->walletRepository->update($id, $data);
    }

    /**
     * Initiate a new Wallet transaction
     *
     * @param string $wallet_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateTransaction(string $wallet_id, array $transactionDetails): Transaction
    {
        return $this->transactionService->initiateWalletPaymentTransaction($wallet_id, $transactionDetails);
    }
}
