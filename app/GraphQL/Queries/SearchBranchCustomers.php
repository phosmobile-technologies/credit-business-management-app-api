<?php

namespace App\GraphQL\Queries;

use App\Services\BranchService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class SearchBranchCustomers
{
    /**
     * @var BranchService
     */
    private $branchService;

    /**
     * GetBranchLoans constructor.
     *
     * @param BranchService $branchService
     */
    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $searchString = isset($args['search_query']) ? $args['search_query'] : null;
        $startDate = isset($args['start_date']) ? $args['start_date'] : null;
        $endDate = isset($args['end_date']) ? $args['end_date'] : null;

        return $this->branchService->searchBranchCustomersQuery($args['branch_id'], $searchString, $startDate, $endDate);
    }
}
