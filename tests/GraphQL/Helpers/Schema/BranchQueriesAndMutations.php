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

    /**
     * Search the customers that belong to a branch.
     *
     * @return string
     */
    public static function searchBranchCustomers()
    {
        return '
            query SearchBranchCustomers($branch_id: ID!, $search_query: String) {
                SearchBranchCustomers(branch_id: $branch_id, search_query: $search_query) {
                   paginatorInfo {
                      count
                      currentPage
                      firstItem
                      total
                    }
                    data {
                      id
                      first_name
                      last_name
                    }
                  }
            }
        ';
    }

    /**
     * Get the loans that belong to a branch.
     *
     * @return string
     */
    public static function getBranchLoans()
    {
        return '
            query GetBranchLoans($branch_id: ID!, $loan_condition_status: LoanConditionStatus) {
                GetBranchLoans(branch_id: $branch_id, loan_condition_status: $loan_condition_status) {
                   paginatorInfo {
                      count
                      currentPage
                      firstItem
                      total
                    }
                    data {
                      id
                      loan_amount
                      loan_condition_status
                      user {
                        first_name
                        last_name
                      }
                    }
                  }
            }
        ';
    }



    /**
     * Get the loans that belong to a branch.
     *
     * @return string
     */
    public static function GetCompany()
    {
        return '
            query GetCompany {
                GetCompany{
                   id
                    name
                    location
                    branches{
                    id
                    name
                    location
            }
            }
            }
        ';
    }

    /**
     * Get the transactions that belong to a branch.
     *
     * @return string
     */
    public static function getBranchTransactions()
    {
        return '
            query GetBranchTransactions($branch_id: ID!, $loan_condition_status: LoanConditionStatus) {
                GetBranchLoans(branch_id: $branch_id, loan_condition_status: $loan_condition_status) {
                   paginatorInfo {
                      count
                      currentPage
                      firstItem
                      total
                    }
                    data {
                      id
                      loan_amount
                      loan_condition_status
                      user {
                        first_name
                        last_name
                      }
                    }
                  }
            }
        ';
    }

}
