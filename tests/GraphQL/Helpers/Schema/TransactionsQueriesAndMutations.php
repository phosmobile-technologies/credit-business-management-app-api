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

    /**
     * Mutation for processing (approving/disapproving) a loan repayment transaction.
     *
     * @return string
     */
    public static function processLoanRepaymentTransaction()
    {
        return '
            mutation ProcessLoanRepaymentTransaction(
                $transaction_id: ID!
                $loan_id: ID!
                $action: TransactionProcessingType!
                $message: String
            ) {
                ProcessLoanRepaymentTransaction(
                    transaction_id: $transaction_id
                    loan_id: $loan_id
                    action: $action
                    message: $message
                ) {
                    id
                    transaction_amount
                    transaction_status
                }
            }
        ';
    }

    /**
     * Query for getting a transaction by ID.
     *
     * @return string
     */
    public static function GetTransactionById() {
        return '
            query GetTransactionById($id: ID!) {
                GetTransactionById(id: $id) {
                    id
                }
            }
        ';
    }
}
