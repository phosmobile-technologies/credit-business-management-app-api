<?php

namespace App\Repositories;


use App\Models\CompanyBranch;
use App\Models\enums\ContributionType;
use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\Models\enums\RegistrationSource;
use App\Models\enums\TransactionStatus;
use App\Models\enums\TransactionType;
use App\Models\Enums\UserRoles;
use App\Repositories\Interfaces\CompanyBranchRepositoryInterface;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class CompanyBranchRepository implements CompanyBranchRepositoryInterface
{

    /**
     * Find a CompanyBranch by id.
     *
     * @param string $branch_id
     * @return CompanyBranch|null
     */
    public function find(string $branch_id): ?CompanyBranch
    {
        return CompanyBranch::findOrFail($branch_id);
    }

    /**
     * Find the customers that belong to a branch.
     *
     * @param string $branch_id
     * @return Collection
     */
    public function findCustomers(string $branch_id): Collection
    {
        $branch = $this->find($branch_id);

        return $branch->customers()->role([UserRoles::CUSTOMER])->get();
    }


    /**
     * Get the eloquent query builder that can get customers that belong to a branch.
     *
     * @param string $branch_id
     * @return HasManyThrough
     */
    public function findCustomersQuery(string $branch_id): HasManyThrough
    {
        $branch = $this->find($branch_id);

        return $branch->customers()->role([UserRoles::CUSTOMER]);
    }

    /**
     * Get the eloquent query builder that can get admins that belong to a branch.
     *
     * @param string     $branch_id
     * @param array|null $roles
     * @return HasManyThrough
     */
    public function findAdminsQuery(string $branch_id, ?array $roles): HasManyThrough
    {
        $branch = $this->find($branch_id);

        if (isset($roles)) {
            return $branch->customers()->role($roles);
        }

        return $branch->customers()->role([UserRoles::ADMIN_STAFF, UserRoles::BRANCH_ACCOUNTANT, UserRoles::BRANCH_MANAGER,
            UserRoles::ADMIN_MANAGER, UserRoles::ADMIN_ACCOUNTANT]);
    }

    /**
     * Find the loans that belong to a branch.
     *
     * @param string $branch_id
     * @return Collection
     */
    public function findLoans(string $branch_id): Collection
    {
        $branch = $this->find($branch_id);

        return $branch->loans()->get();
    }

    /**
     * Get the eloquent query builder that can get loans that belong to a branch.
     *
     * @param string $branch_id
     * @return HasManyThrough
     */
    public function findLoansQuery(string $branch_id): HasManyThrough
    {
        $branch = $this->find($branch_id);

        return $branch->loans();
    }

    /**
     * Get the eloquent query builder that can get contribution plans that belong to a branch.
     *
     * @param string $branch_id
     * @return HasManyThrough
     */
    public function findContributionPlansQuery(string $branch_id): HasManyThrough
    {
        $branch = $this->find($branch_id);

        return $branch->contribubtionPlans();
    }

    /**
     * Get the eloquent query builder that can get loan applications that belong to a branch.
     *
     * @param string      $branch_id
     * @param null|string $isAssigned
     * @return HasManyThrough
     */
    public function findLoanApplicationsQuery(string $branch_id, ?string $isAssigned): HasManyThrough
    {
        $branch = $this->find($branch_id);
        $query  = $branch->loanApplications();

        if (isset($isAssigned)) {
            if ($isAssigned) {
                $query->whereNotNull('loan_applications.assignee_id');
            } else {
                $query->whereNull('loan_applications.assignee_id');
            }
        }

        return $query;
    }

    /**
     * Search/Filter the customers for a branch.
     *
     * @param string      $branch_id
     * @param null|string $search_query
     * @param Date|null   $start_date
     * @param Date|null   $end_date
     * @return HasManyThrough
     */
    public function searchBranchCustomers(string $branch_id, ?string $search_query, ?Date $start_date, ?Date $end_date): HasManyThrough
    {
        $branch = $this->find($branch_id);

        return $branch->customers()->role([UserRoles::CUSTOMER])->where(function ($query) use ($search_query, $start_date, $end_date) {


            if (isset($search_query)) {
                $query->where(DB::raw('lower(users.first_name)'), 'like', "%{$search_query}%")
                    ->orWhere(DB::raw('lower(users.last_name)'), 'like', "%{$search_query}%");
            }

            if (isset($start_date)) {
                $query->whereDate('created_at', '>=', $start_date);
            }

            if (isset($end_date)) {
                $query->whereDate('created_at', '<=', $end_date);
            }

        });
    }

    /**
     * Get the eloquent query builder that can get transactions that belong to a branch.
     *
     * @param string $branch_id
     * @param array  $queryParameters
     * @return mixed
     */
    public function findTransactionsQuery(string $branch_id, array $queryParameters = [])
    {
        $branch = $this->find($branch_id);

        return $branch->transactions()->where(function ($query) use ($queryParameters) {

            if (isset($queryParameters['min_amount'])) {
                $query->where('transaction_amount', '>=', floatval($queryParameters['min_amount']));
            }

            if (isset($queryParameters['max_amount'])) {
                $query->where('transaction_amount', '<=', floatval($queryParameters['max_amount']));
            }

            if (isset($queryParameters['start_date'])) {
                $query->whereDate('created_at', '>=', $queryParameters['start_date']);
            }

            if (isset($queryParameters['end_date'])) {
                $query->whereDate('created_at', '<=', $queryParameters['end_date']);
            }

        });
    }

    /**
     * @param $branch_id
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function computeBranchReport($branch_id, $start_date, $end_date)
    {
        $branch = $this->find($branch_id);

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

    /**
     * @param $branch_id
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function computeBranchContributionReport($branch_id, $start_date, $end_date)
    {
        $branch = $this->find($branch_id);
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
