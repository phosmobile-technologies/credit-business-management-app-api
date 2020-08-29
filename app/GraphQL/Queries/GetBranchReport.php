<?php

namespace App\GraphQL\Queries;

use App\Services\BranchService;

class GetBranchReport
{
    /**
     * @var BranchService
     */
    private $branchService;

    /**
     * GetBranchReport constructor.
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
        $start_date = isset($args['start_date'])?$args['start_date']:null;
        $end_date = isset($args['end_date'])?$args['end_date']:null;
        return $this->branchService->getBranchReport($args['branch_id'],$start_date, $end_date);

    }
}
