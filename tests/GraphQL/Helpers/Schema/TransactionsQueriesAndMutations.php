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

    /**
     * Mutation for initiating a contribution plan transaction.
     *
     * @return string
     */
    public static function initiateContributionPlanTransaction()
    {
        return '
            mutation InitiateContributionPlanTransaction($input: CreateContributionPlanTransactionInput!) {
                InitiateContributionPlanTransaction(input: $input) {
                     id
                     transaction_amount
                }
            }
        ';
    }

    /**
     * Mutation for processing (approving/disapproving) a transaction.
     *
     * @return string
     */
    public static function processTransaction()
    {
        return '
            mutation ProcessTransaction(
                $transaction_id: ID!
                $action: TransactionProcessingType!
                $message: String
            ) {
                ProcessTransaction(
                    transaction_id: $transaction_id
                    action: $action
                    message: $message
                ) {
                    id
                    transaction_date
                    transaction_type
                    transaction_amount
                    transaction_medium
                    transaction_purpose
                    transaction_status
                }
            }
        ';
    }

    /**
     * Mutation for initiating a transaction request.
     *
     * @return string
     */
    public static function initiateTransaction()
    {
        return '
            mutation InitiateTransaction($input: CreateTransactionInput!) {
                InitiateTransaction(input: $input) {
                     id
                     transaction_amount
                }
            }
        ';
    }
}
