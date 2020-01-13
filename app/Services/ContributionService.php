<?php

namespace App\Services;


use App\Models\MemberContribution;
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
     * @return MemberContribution
     */
    public function create(array $contributionData): MemberContribution
    {
        return $this->contributionRepository->create($contributionData);
    }

    /**
     * Update a contribution
     *
     * @param array $contributionData
     * @return MemberContribution
     */
    public function update(array $contributionData): MemberContribution
    {
        $contributionData = collect($contributionData);
        $id = $contributionData['id'];
        $data = $contributionData->except(['id'])->toArray();

        return $this->contributionRepository->update($id, $data);
    }
}
