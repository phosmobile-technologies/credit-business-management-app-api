<?php

namespace App\Services;


use App\Models\ContributionPlan;
use App\Models\enums\ContributionStatus;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\TransactionStatus;
use App\Models\Transaction;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use Carbon\Carbon;

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
     * @param string $customer_id
     * @return mixed
     */
    public function getContributionPlan(string $customer_id)
    {
        $user_contributions =  collect(ContributionPlan::where('user_id',$customer_id)->get());
        $computedContributions = [];
        foreach ($user_contributions as $contribution){
           $userContribution =  collect($contribution)->toArray();
            $currentDate = Carbon::now(); //get a carbon instance with created_at as date
            $payBackDate =  Carbon::parse($userContribution['contribution_payback_date']);

            $userContribution['contribution_status'] = $this->computeContributionPlanStatus($currentDate,$payBackDate,$userContribution['contribution_duration']);
           array_push($computedContributions,$userContribution);
        }

        return $computedContributions;

    }

    public function computeContributionPlanStatus( $currentDate, $payBackDate, float $balance)
    {

        if($balance <=0 && $currentDate < $payBackDate){
            return ContributionStatus::INACTIVE;
        }else{

            if($currentDate > $payBackDate){
                return ContributionStatus::COMPLETED;
            }else{
                return ContributionStatus::ACTIVE;
            }

        }
    }




}
