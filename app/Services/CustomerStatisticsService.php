<?php

namespace App\Services;

use App\Models\enums\ContributionType;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;

/**
 * Class CustomerStatisticsService
 *
 * Service for the customer statistics
 *
 * @package App\Services
 */
class CustomerStatisticsService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * CustomerStatisticsService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $customer_id
     * @return array
     */
    public function getCustomerStatistics(string $customer_id): array
    {
        $customer = $this->userRepository->find($customer_id);

        return [
            'loan_statistics' => $this->getCustomerLoanStatistics($customer),
            'contribution_plan_statistics' => $this->getCustomerContributionPlanStatistics($customer)
        ];
    }

    /**
     * Compute various statistics for a customer's loan
     *
     * @param User $user
     * @return array
     */
    private function getCustomerLoanStatistics(User $user): array
    {
        //@TODO Figure out how to calculate the next_due_payment amount and date for a loan.

        // Set the default values for loan statistics
        $loanStatistics = [
            'loan_balance' => 0.00,
            'next_due_payment' => 0.00,
            'next_repayment_date' => 'N/A',
            'default_charges' => 0.00,
            'total_paid_amount' => 0.00,
            'active_loan' => false,
        ];

        $loan = $user->loans()->first();


        if ($loan->loan_condition_status === LoanConditionStatus::ACTIVE) {
            $loanStatistics['active_loan'] = true;
            $loanStatistics['loan_balance'] = $loan->loan_balance;
            $loanStatistics['total_paid_amount'] = $loan->loan_amount - $loan->loan_balance;

            if (($loan->loan_default_status === LoanDefaultStatus::DEFAULTING) && isset($loan->num_of_default_days)) {
                $loanStatistics['default_charges'] = $loan->default_amount * $loan->num_of_default_days;
            }
        }


        return $loanStatistics;
    }

    /**
     * @param User $user
     * @return array
     */
    private function getCustomerContributionPlanStatistics(User $user): array
    {
        $contributionPlanStatistics = [
            'total_contribution_amount' => 0.00,
            'goal_contribution_sum' => 0.00,
            'fixed_contribution_sum' => 0.00,
            'locked_contribution_sum' => 0.00,
            'wallet_balance' => $user->wallet->wallet_balance,
        ];

        $contributionPlans = $user->contributionPlans;

        if (count($contributionPlans)) {
            $contributionPlanStatistics['total_contribution_amount'] = $contributionPlans->sum('contribution_amount');
            $contributionPlanStatistics['goal_contribution_sum'] = $contributionPlans->where('contribution_type', ContributionType::GOAL)->sum('contribution_amount');
            $contributionPlanStatistics['fixed_contribution_sum'] = $contributionPlans->where('contribution_type', ContributionType::FIXED)->sum('contribution_amount');
            $contributionPlanStatistics['locked_contribution_sum'] = $contributionPlans->where('contribution_type', ContributionType::LOCKED)->sum('contribution_amount');
        }

        return $contributionPlanStatistics;
    }
}
