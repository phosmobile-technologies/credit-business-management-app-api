<?php

namespace Tests\GraphQL\Helpers\Schema;


class LoanDisbursementQueriesAndMutations
{
    /**
     * Mutation for disbursing a loan.
     *
     * @return string
     */
    public static function disburseLoan() {
        return '
            mutation DisburseLoan($input: DisburseLoanInput!) {
                DisburseLoan(input: $input) {
                    id
                    amount_disbursed
                    loan_amount
                    loan_balance
                    totalInterestAmount
                }
            }
        ';
    }
}
