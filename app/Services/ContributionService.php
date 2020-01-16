<?php

namespace App\Services;


use App\Models\ContributionPlan;
use App\Repositories\Interfaces\ContributionRepositoryInterface;

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
     * ContributionService constructor.
     * @param ContributionRepositoryInterface $contributionRepository
     */
    public function __construct(ContributionRepositoryInterface $contributionRepository)
    {
        $this->contributionRepository = $contributionRepository;
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
}
