<?php

namespace App\Services;


use App\GraphQL\Errors\GraphqlError;
use App\Models\ContributionPlan;
use App\Models\enums\TransactionMedium;
use App\Models\enums\ContributionType;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionProcessingActions;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ContributionService
 *
 * Service handling contributions.
 *
 * @package App\Services
 */
class ContributionService
{
    /**
     * @var ContributionRepositoryInterface
     */
    private $contributionRepository;

    /**
     * @var TransactionService
     */
    private $transactionService;
    /**
     * @var WalletRepository
     */
    private $walletRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ContributionService constructor.
     * @param ContributionRepositoryInterface $contributionRepository
     * @param TransactionService $transactionService
     * @param WalletRepository $walletRepository
     * @param UserRepository $userRepository
     */
    public function __construct(ContributionRepositoryInterface $contributionRepository, TransactionService $transactionService,
                                WalletRepository $walletRepository, UserRepository $userRepository)
    {
        $this->contributionRepository = $contributionRepository;
        $this->transactionService = $transactionService;
        $this->walletRepository = $walletRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a contribution
     *
     * @param array $contributionData
     * @return ContributionPlan
     */
    public function create(array $contributionData): ContributionPlan
    {
        // Ensure that the default value when creating a contribution is set
        $contributionData['balance'] = null;

        switch ($contributionData['type']){
            case ContributionType::GOAL:{
                $contributionData['interest_rate'] = 10;
                break;
            }
            case ContributionType::LOCKED:{
                if($contributionData['duration'] <= 3){
                    $contributionData['interest_rate'] = 6;
                }elseif($contributionData['duration'] <= 6){
                    $contributionData['interest_rate'] = 8;
                }elseif ($contributionData['duration'] <= 9){
                    $contributionData['interest_rate'] = 10;
                }else{
                    $contributionData['interest_rate'] = 12;
                }

                break;
            }
            case ContributionType::FIXED:{
                $contributionData['interest_rate'] = 10;
                break;
            }
        }

        return $this->contributionRepository->create($contributionData);
    }

    /**
     * Update a contribution
     *
     * @param array $contributionData
     * @return ContributionPlan
     */
    public function update(array $contributionData): ContributionPlan
    {
        $contributionData = collect($contributionData);
        $id = $contributionData['id'];
        $data = $contributionData->except(['id'])->toArray();

        return $this->contributionRepository->update($id, $data);
    }

    /**
     * Initiate a new contribution plan transaction
     *
     * @param string $contribution_plan_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateTransaction(string $contribution_plan_id, array $transactionDetails): Transaction
    {
        return $this->transactionService->initiateContributionPlanPaymentTransaction($contribution_plan_id, $transactionDetails);
    }

    /**
     * Withdraw funds from a contribution plan to a wallet.
     *
     * @param string $contribution_plan_id
     * @param string $wallet_id
     * @param float $amount
     * @param string $user_id
     * @return ContributionPlan
     * @throws GraphqlError
     */
    public function withdrawToWallet(string $contribution_plan_id, string $wallet_id, float $amount, string $user_id): ContributionPlan {
        $contributionPlan = $this->contributionRepository->find($contribution_plan_id);

        if($amount > $contributionPlan->balance) {
            throw new GraphqlError("Cannot withdraw {$amount} from contribution plan with {$contributionPlan->balance} as balance");
        }

        $user = $this->userRepository->find($user_id);

        // Start a database for initiating and approving the necessary transactions
        DB::transaction(function() use ($contribution_plan_id, $amount, $wallet_id, $user) {
            // Initiate Transaction For Funding wallet and approve it, do the same for withdrawing from contribution plan
            $contributionPlanWithdrawalTransaction = $this->transactionService->initiateTransaction($contribution_plan_id, [
                'transaction_date' => Carbon::now()->toDateString(),
                'transaction_type' => TransactionType::CONTRIBUTION_WITHDRAWAL,
                'transaction_amount' => $amount,
                'transaction_medium' => TransactionMedium::ONLINE,
                'transaction_purpose' => "Online withdrawing from user contribution plan to fund wallet",
            ]);

            list($contributionPlanWithdrawalTransaction, $amountSentToWallet) = $this->transactionService->processTransaction($user, $contributionPlanWithdrawalTransaction->id,
                TransactionProcessingActions::APPROVE, "Approved as an online user contribution plan withdrawal transaction");

            $walletFundingTransaction = $this->transactionService->initiateTransaction($wallet_id, [
                'transaction_date' => Carbon::now()->toDateString(),
                'transaction_type' => TransactionType::WALLET_PAYMENT,
                'transaction_amount' => $amountSentToWallet,
                'transaction_medium' => TransactionMedium::ONLINE,
                'transaction_purpose' => "Online Wallet funding from user contribution plan",
            ]);

            $this->transactionService->processTransaction($user, $walletFundingTransaction->id,
                TransactionProcessingActions::APPROVE, "Approved as an online user wallet funding transaction");
        });

        return $this->contributionRepository->find($contribution_plan_id);
    }
}
