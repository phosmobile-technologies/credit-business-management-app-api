<?php

namespace App\Repositories;


use App\Models\CompanyBranch;
use App\Models\Enums\UserRoles;
use App\Repositories\Interfaces\CompanyBranchRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

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
     * Get the eloquent query builder that can get loan applications that belong to a branch.
     *
     * @param string $branch_id
     * @return HasManyThrough
     */
    public function findLoanApplicationsQuery(string $branch_id): HasManyThrough
    {
        $branch = $this->find($branch_id);

        return $branch->loanApplications();
    }

    /**
     * Search/Filter the customers for a branch.
     *
     * @param string $branch_id
     * @param null|string $search_query
     * @param Date|null $start_date
     * @param Date|null $end_date
     * @return HasManyThrough
     */
    public function searchBranchCustomers(string $branch_id, ?string $search_query, ?Date $start_date, ?Date $end_date): HasManyThrough
    {
        $branch = $this->find($branch_id);

        return $branch->customers()->role([UserRoles::CUSTOMER])->where(function ($query) use ($search_query, $start_date, $end_date) {

            if(isset($search_query)) {
                $query->where('first_name', 'like', "%{$search_query}%")
                    ->orWhere('last_name', 'like', "%{$search_query}%");
            }

            if(isset($start_date)) {
                $query->whereDate('created_at', '>=', $start_date);
            }

            if(isset($end_date)) {
                $query->whereDate('created_at', '<=', $end_date);
            }

        });
    }
}
