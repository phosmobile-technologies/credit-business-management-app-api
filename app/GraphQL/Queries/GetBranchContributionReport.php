<?php

namespace App\GraphQL\Queries;

use App\Services\BranchService;

class GetBranchContributionReport
{
    /**
     * @var BranchService
     */
    private $branchService;

    /**
     * GetBranchContributionReport constructor.
     * @param BranchService $branchService
     */
    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return $this->branchService->getBranchContributionReport();
    }
}
