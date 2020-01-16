<?php

namespace Tests\GraphQL\Helpers\Schema;


class TransactionsQueriesAndMutations
{
    /**
     * Mutation for initiating a loan repayment request.
     *
     * @return string
     */
    public static function initiateLoanRepaymentTransaction()
    {
        return '
            mutation InitiateLoanRepaymentTransaction($input: CreateLoanRepaymentTransactionInput!) {
                InitiateLoanRepaymentTransaction(input: $input) {
                     id
                     transaction_amount
                }
            }
        ';
    }
}
