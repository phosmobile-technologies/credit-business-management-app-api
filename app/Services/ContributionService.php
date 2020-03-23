<?php

namespace App\Services;


use App\Models\ContributionPlan;
use App\Models\enums\ContributionStatus;
use App\Models\enums\ContributionType;
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
     * @param $contribution_plan_type
     * @return mixed
     */
    public function getContributionPlan(string $customer_id, $contribution_plan_type=null)
    {

        if(isset($contribution_plan_type)){
            $user_contributions = collect(ContributionPlan::where('user_id',$customer_id)->where('contribution_type',$contribution_plan_type)->get());
        }else{
            $user_contributions = collect(ContributionPlan::where('user_id',$customer_id)->get());
        }

        $computedContributions = [];
        foreach ($user_contributions as $contribution){
           $userContribution =  collect($contribution)->toArray();
            $currentDate = Carbon::now(); //get a carbon instance with created_at as date
            $payBackDate =  Carbon::parse($userContribution['contribution_payback_date']);

            $userContribution['contribution_status'] = $this->computeContributionPlanStatus($currentDate,$payBackDate,                                      $userContribution['contribution_amount']);
            switch ($userContribution['contribution_type']){
                case ContributionType::FIXED:{
                    $userContribution['contribution_interest'] = $this->computeContributionPlanInterest (10, $userContribution['contribution_start_date'], $payBackDate, $userContribution['contribution_amount']);
                    break;
                }

                case ContributionType::LOCKED:{
                    $interestRate = null;
                   if($userContribution['contribution_duration'] <= 3){
                       $interestRate = 6;
                   }elseif($userContribution['contribution_duration'] <= 6){
                       $interestRate = 8;
                   }
                   elseif($userContribution['contribution_duration'] <=9){
                       $interestRate = 10;
                   }
                   elseif($userContribution['contribution_duration'] <=12){
                       $interestRate = 12;
                   }
                    $userContribution['contribution_interest']= $this->computeContributionPlanInterest ($interestRate, $userContribution['contribution_start_date'], $payBackDate, $userContribution['contribution_amount']);
                   break;
                }
                case ContributionType::GOAL:{
                    $userContribution['contribution_interest']= $this->computeContributionPlanInterest (10, $userContribution['contribution_start_date'], $payBackDate, $userContribution['contribution_amount']);
                    break;
                }
            }
           array_push($computedContributions,$userContribution);
        }

        return $computedContributions;

    }

    static function computeContributionPlanStatus( $currentDate, $payBackDate, float $contributionPlanBalance =null)
    {

        if(!isset($contributionPlanBalance) || $contributionPlanBalance <=0 && $currentDate < $payBackDate){
            return ContributionStatus::INACTIVE;
        }else{

            if($currentDate > $payBackDate){
                return ContributionStatus::COMPLETED;
            }else{
                return ContributionStatus::ACTIVE;
            }

        }
    }


    static function computeContributionPlanInterest (float $interestRate, string $startDate, string $payBackDate, float $contributionPlanBalance)
    {
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($startDate);
        $payBackDate = Carbon::parse($payBackDate);

        $contributionPlanDuration =$startDate->diffInDays($payBackDate);
        $contributionPlanDurationSoFar = $startDate->diffInDays($currentDate);

        $interestPerDay = ($interestRate/$contributionPlanDuration)/100 * $contributionPlanBalance;
        $interestSoFar = $interestPerDay*$contributionPlanDurationSoFar;

        return $interestSoFar;

    }






}
