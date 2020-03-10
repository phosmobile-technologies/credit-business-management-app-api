<?php

namespace Tests\GraphQL\Helpers\Schema;


class LoanApplicationQueriesAndMutations
{
    /**
     * Mutation for creating a loan application.
     *
     * @return string
     */
    public static function createLoanApplication() {
        return '
            mutation CreateLoanApplication($input: CreateLoanApplicationInput!) {
                CreateLoanApplication(input: $input) {
                    id
                    user {
                        id
                    }
                    loan_purpose
                    loan_repayment_source
                    loan_amount
                    loan_repayment_frequency
                    tenure
                    expected_disbursement_date
                    created_at
                    updated_at
                }
            }
        ';
    }

    /**
     * Get the loans applications that belong to a branch.
     *
     * @return string
     */
    public static function getBranchLoanApplications()
    {
        return '
            query GetBranchLoanApplications($branch_id: ID!) {
                GetBranchLoanApplications(branch_id: $branch_id) {
                   paginatorInfo {
                      count
                      currentPage
                      firstItem
                      total
                    }
                    data {
                      id
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
