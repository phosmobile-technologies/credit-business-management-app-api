<?php

namespace Tests\GraphQL\Helpers\Schema;


class BranchQueriesAndMutations
{
    /**
     * Query for getting a branch by id.
     *
     * @return string
     */
    public static function getBranchById()
    {
        return '
            query GetBranchById($id: ID!) {
                GetBranchById(id: $id) {
                    id
                    customers {
                        id
                    }
                }
            }
        ';
    }

    /**
     * Get the customers that belong to a branch.
     *
     * @return string
     */
    public static function getBranchCustomers()
    {
        return '
            query GetBranchCustomers($branch_id: ID!) {
                GetBranchCustomers(branch_id: $branch_id) {
                   paginatorInfo {
                      count
                      currentPage
                      firstItem
                      total
                    }
                    data {
                      id
                      first_name
                    }
                  }
            }
        ';
    }
}
