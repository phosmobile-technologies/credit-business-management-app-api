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
}
