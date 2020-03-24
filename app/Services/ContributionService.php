<?php

namespace App\Services;


use App\Models\ContributionPlan;
use App\Models\enums\ContributionType;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\Transaction;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

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
     * ContributionService constructor.
     * @param ContributionRepositoryInterface $contributionRepository
     * @param TransactionService $transactionService
     */
    public function __construct(ContributionRepositoryInterface $contributionRepository, TransactionService $transactionService)
    {
        $this->contributionRepository = $contributionRepository;
        $this->transactionService = $transactionService;
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

        $contributionData['contribution_balance'] = null;
        switch ($contributionData['contribution_type']){
            case ContributionType::GOAL:{
                $contributionData['contribution_interest_rate'] = 10;
                break;
            }
            case ContributionType::LOCKED:{
                    if($contributionData['contribution_duration'] <= 3){
                        $contributionData['contribution_interest_rate'] = 6;
                    }elseif($contributionData['contribution_duration'] <= 6){
                        $contributionData['contribution_interest_rate'] = 8;
                    }elseif ($contributionData['contribution_duration'] <= 9){
                        $contributionData['contribution_interest_rate'] = 10;
                    }else{
                        $contributionData['contribution_interest_rate'] = 12;
                    }

                break;
            }
            case ContributionType::FIXED:{
                $contributionData['contribution_interest_rate'] = 10;
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
}
