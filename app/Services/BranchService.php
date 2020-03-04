<?php

namespace App\Services;

use App\Models\CompanyBranch;
use App\Models\Transaction;
use App\Models\enums\TransactionType;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\CompanyBranchRepositoryInterface;
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

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(CompanyBranchRepositoryInterface $branchRepository,  TransactionService $transactionService, UserRepositoryInterface $userRepository)
    {
        $this->branchRepository = $branchRepository;
        $this->transactionService = $transactionService;
        $this->userRepository = $userRepository;
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

    /**
     * Get the query builder for Transactions that a belong to a branch.
     *
     * @param string $branch_id
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getBranchTransactionsQuery(string $branch_id)
    {
        return $this->branchRepository->findTransactionsQuery($branch_id);
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
    public function searchBranchCustomersQuery(string $branch_id, ?string $search_query, ?Date $start_date, ?Date $end_date) {
        return $this->branchRepository->searchBranchCustomers($branch_id, $search_query, $start_date, $end_date);
    }

    /**
     * Initiate a new Company transaction
     *
     * @param string $company_id
     * @param array $transactionDetails
     * @return Transaction
     */
    public function initiateTransaction(string $branch_id, array $transactionDetails): Transaction
    {
        return $this->transactionService->initiateCompanyWithdrawalTransaction($branch_id, $transactionDetails);
    }
}
