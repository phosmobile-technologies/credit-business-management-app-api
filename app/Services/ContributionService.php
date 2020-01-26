<?php

namespace App\Services;


use App\Models\ContributionPlan;
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
