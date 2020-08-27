<?php

namespace App\Services;

use App\Models\ContributionPlan;
use App\Models\enums\ContributionType;
use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\enums\RegistrationSource;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Repositories\Interfaces\CompanyBranchRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

/**
 * Class BranchService
 * @package App\Services
 */
class BranchService
{
    /**
     * @var CompanyBranchRepositoryInterface
     */
    private $branchRepository;

    public function __construct(CompanyBranchRepositoryInterface $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    /**
     * Get the customers that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Support\Collection
     */
    public function getBranchCustomers(string $branch_id)
    {
        return $this->branchRepository->findCustomers($branch_id);
    }

    /**
     * Get the query builder for customers that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchCustomersQuery(string $branch_id)
    {
        return $this->branchRepository->findCustomersQuery($branch_id);
    }

    /**
     * Get the query builder for customers that a belong to a branch.
     *
     * @param string $branch_id
     * @param array|null $roles
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchAdminsQuery(string $branch_id, ?array $roles)
    {
        return $this->branchRepository->findAdminsQuery($branch_id, $roles);
    }

    /**
     * Get the loans that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Support\Collection
     */
    public function getBranchLoans(string $branch_id)
    {
        return $this->branchRepository->findLoans($branch_id);
    }

    /**
     * Get the query builder for loans that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchLoansQuery(string $branch_id)
    {
        return $this->branchRepository->findLoansQuery($branch_id);
    }

    /**
     * Get the query builder for loans that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchContributionPlansQuery(string $branch_id)
    {
        return $this->branchRepository->findContributionPlansQuery($branch_id);
    }

    /**
     * Get the query builder for loan applications that a belong to a branch.
     *
     * @param string $branch_id
     * @param null|string $isAssigned
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchLoanApplicationsQuery(string $branch_id, ?string $isAssigned)
    {
        return $this->branchRepository->findLoanApplicationsQuery($branch_id, $isAssigned);
    }

    /**
     * Search/Filter the branch customers.
     *
     * @param string $branch_id
     * @param null|string $search_query
     * @param Date|null $start_date
     * @param Date|null $end_date
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function searchBranchCustomersQuery(string $branch_id, ?string $search_query, ?Date $start_date, ?Date $end_date)
    {
        return $this->branchRepository->searchBranchCustomers($branch_id, $search_query, $start_date, $end_date);
    }

    /**
     * Get the query builder for transactions that a belong to a branch.
     *
     * @param string $branch_id
     * @param array $queryParameters
     * @return mixed
     */
    public function getBranchTransactionsQuery(string $branch_id, array $queryParameters)
    {
        return $this->branchRepository->findTransactionsQuery($branch_id, $queryParameters);
    }

    /**
     * @param string $branch_id
     * @return array
     */
    public function getBranchStatistics(string $branch_id): array
    {
        $branch = $this->branchRepository->find($branch_id);

        $statistics = [
            'branch_customers' => 0,
            'loan_applications' => 0,
            'defaulting_loans' => 0,
            'active_contribution_plans' => 0,
            'transactions' => 0
        ];

        $statistics['branch_customers'] = $branch->customers()->count();
        $statistics['loan_applications'] = $branch->loanApplications()->count();
        $statistics['defaulting_loans'] = $branch->loans()->where('loan_default_status', '=', LoanDefaultStatus::DEFAULTING)->count();
        $statistics['active_contribution_plans'] = $branch->contributionPlans()->where('contribution_plans.status', '=', ContributionPlan::STATUS_ACTIVE)->count();
        $statistics['transactions'] = $branch->transactions()->count();

        return $statistics;
    }

    /**
     * @param $branch_id
     * @param null $start_date
     * @param null $end_date
     * @return array
     */
    public function getBranchReport($branch_id, $start_date = null, $end_date = null)
    {
        $branch = $this->branchRepository->find($branch_id);

        $reports["total_online_branch_members"] = $branch->customers()
            ->where("registration_source", RegistrationSource::ONLINE)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('users.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('users.created_at', '<=', $end_date);
            })
            ->count();
        $reports["backend_branch_members"] = $branch->customers()
            ->where("registration_source", RegistrationSource::BACKEND)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('users.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('users.created_at', '<=', $end_date);
            })
            ->count();
        $reports["total_number_of_loans_disbursed"] = $branch->loans()
            ->where('disbursement_status', DisbursementStatus::DISBURSED)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->count();
        $reports["total_disbursed_amount"] = $branch->loans()
            ->where('disbursement_status', DisbursementStatus::DISBURSED)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->sum("loan_amount");
        $reports["total_loan_applications"] = $branch->loanApplications()
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loan_applications.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loan_applications.created_at', '<=', $end_date);
            })
            ->count();
        $reports["total_new_customers"] = $branch->customers()->count();
        $reports["total_number_of_defaulting_loans"] = $branch->loans()
            ->where('loan_default_status', LoanDefaultStatus::DEFAULTING)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->count();
        $reports["total_default_amount"] = $branch->loans()
            ->where('loan_default_status', LoanDefaultStatus::DEFAULTING)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->sum("loan_amount");
        $reports["total_loan_repayments"] = $branch->transactions()
            ->where("transaction_type", TransactionType::LOAN_REPAYMENT)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('transactions.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('transactions.created_at', '<=', $end_date);
            })
            ->count();
        $reports["total_loan_balance"] = $branch->loans()
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->get()->sum('loan_balance');
        $reports["total_interest_amount"] = $branch->loans()
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->get()->sum(function ($loan) {
            return $loan->getTotalInterestAmountAttribute();
        });
        $reports["total_nonperforming_loans"] = $branch->loans()
            ->where('loan_condition_status', LoanConditionStatus::NONPERFORMING)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('loans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('loans.created_at', '<=', $end_date);
            })
            ->count();

        return $reports;
    }

    public function getBranchContributionReport($branch_id, $start_date = null, $end_date = null)
    {
        $branch = $this->branchRepository->find($branch_id);
        $lockedContributions = $branch->contribubtionPlans()
            ->where("type", ContributionType::LOCKED)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('contribution_plans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('contribution_plans.created_at', '<=', $end_date);
            });
        $goalContributions = $branch->contribubtionPlans()->where("type", ContributionType::GOAL)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('contribution_plans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('contribution_plans.created_at', '<=', $end_date);
            });
        $fixedContributions = $branch->contribubtionPlans()->where("type", ContributionType::FIXED)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('contribution_plans.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('contribution_plans.created_at', '<=', $end_date);
            });
        $contributionReport["total_wallet_balance"] = $branch->customers()->get()->sum(function ($user) {
            return $user->wallet->wallet_balance;
        });
        $contributionReport["total_amount_withdrawn"] = $branch->transactions()
            ->where("transaction_type", TransactionType::CONTRIBUTION_PAYMENT)
            ->where("transaction_status", TransactionStatus::COMPLETED)
            ->when($start_date, function ($query) use ($start_date) {
                return $query->whereDate('transactions.created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                return $query->whereDate('transactions.created_at', '<=', $end_date);
            })
            ->sum("transaction_amount");
        $contributionReport["total_goal_contribution"] = $goalContributions->count();
        $contributionReport["total_fixed_contribution"] = $fixedContributions->count();
        $contributionReport["total_locked_contribution"] = $lockedContributions->count();
        $contributionReport["total_locked_interest"] = $lockedContributions->get()->sum(function ($contribution) {
            return $contribution->getContributionInterest();
        });
        $contributionReport["total_fixed_interest"] = $fixedContributions->get()->sum(function ($contribution) {
            return $contribution->getContributionInterest();
        });
        $contributionReport["total_goal_interest"] = $goalContributions->get()->sum(function ($contribution) {
            return $contribution->getContributionInterest();
        });
        $contributionReport["total_penalties"] = 0;

        return $contributionReport;
    }

}
