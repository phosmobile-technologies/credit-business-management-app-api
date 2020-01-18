<?php

namespace App\Services;

use App\Repositories\Interfaces\CompanyBranchRepositoryInterface;

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
     * Get the query builder for loan applications that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchLoanApplicationsQuery(string $branch_id)
    {
        return $this->branchRepository->findLoanApplicationsQuery($branch_id);
    }
}
