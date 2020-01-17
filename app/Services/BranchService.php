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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getBranchCustomersQuery(string $branch_id)
    {
        return $this->branchRepository->findCustomersQuery($branch_id);
    }
}
