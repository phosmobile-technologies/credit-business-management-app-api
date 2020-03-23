<?php

namespace App\Services;


use App\Models\ContributionPlan;
use App\Models\enums\TransactionMedium;
use App\Models\enums\TransactionProcessingActions;
use App\Models\Wallet;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Transaction;
use App\GraphQL\Errors\GraphqlError;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
     * @var ContributionRepositoryInterface
     */
    private $contributionRepository;

    /**
     * WalletService constructor.
     * @param WalletRepositoryInterface $walletRepository
     * @param TransactionService $transactionService
     * @param UserRepositoryInterface $userRepository
     * @param ContributionRepositoryInterface $contributionRepository
     */
    public function __construct(WalletRepositoryInterface $walletRepository, TransactionService $transactionService,
                                UserRepositoryInterface $userRepository, ContributionRepositoryInterface $contributionRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->transactionService = $transactionService;
        $this->userRepository = $userRepository;
        $this->contributionRepository = $contributionRepository;
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

    /**
     * Deposit funds withdrawn from a wallet  into a contribution plan.
     *
     * @param string $contribution_plan_id
     * @param string $wallet_id
     * @param float $amount
     * @param string $user_id
     * @return ContributionPlan
     * @throws GraphqlError
     */
    public function fundContributionPlan(string $contribution_plan_id, string $wallet_id, float $amount, string $user_id): ContributionPlan
    {
        $wallet = $this->walletRepository->find($wallet_id);

        if ($wallet->wallet_balance < $amount) {
            throw new GraphqlError("Cannot withdraw {$amount} from wallet with only {$wallet->wallet_balance} balance");
        }

        $user = $this->userRepository->find($user_id);

        // Initiate Transaction For Funding wallet and approve it, do the same for contribution plan
        $walletWithdrawalTransaction = $this->transactionService->initiateTransaction($wallet_id, [
            'transaction_date' => Carbon::now()->toDateString(),
            'transaction_type' => TransactionType::WALLET_WITHDRAWAL,
            'transaction_amount' => $amount,
            'transaction_medium' => TransactionMedium::ONLINE,
            'transaction_purpose' => "Withdrawn to fund user contribution plan",
        ]);

        $contributionPlanFundingTransaction = $this->transactionService->initiateTransaction($contribution_plan_id, [
            'transaction_date' => Carbon::now()->toDateString(),
            'transaction_type' => TransactionType::CONTRIBUTION_PAYMENT,
            'transaction_amount' => $amount,
            'transaction_medium' => TransactionMedium::ONLINE,
            'transaction_purpose' => "Funding user contribution plan with money withdrawn from wallet",
        ]);

        $this->transactionService->processTransaction($user, $walletWithdrawalTransaction->id,
            TransactionProcessingActions::APPROVE, "Approved as an online user wallet withdrawal transaction");

        $this->transactionService->processTransaction($user, $contributionPlanFundingTransaction->id,
            TransactionProcessingActions::APPROVE, "Approved as an online user contribution plan funding transaction");

        return $this->contributionRepository->find($contribution_plan_id);
    }
}
