<?php

namespace Tests\GraphQL\Helpers\Schema;


class LoanQueriesAndMutations
{
    /**
     * Mutation for creating a loan.
     *
     * @return string
     */
    public static function CreateLoanMutation() {
        return '
            mutation CreateLoan($input: CreateLoanInput!) {
                CreateLoan(input: $input) {
                    loan_amount
                    loan_identifier
                    loan_amount
                    loan_repayment_frequency
                    loan_balance
                    next_due_payment
                    disbursement_status
                    application_status
                    loan_condition_status
                    loan_default_status
                }
            }
        ';
    }

    /**
     * Mutation for updating a loan's application status
     *
     * @return string
     */
    public static function UpdateLoanApplicationStatus() {
       return '
            mutation UpdateLoanApplicationStatus(
                $loan_id: ID!,
                $application_status: LoanApplicationStatus!,
                $message: String
            ) {
                UpdateLoanApplicationStatus(loan_id: $loan_id, application_status: $application_status, message: $message) {
                    application_status
                }
            }
       ';
    }

    /**
     * Query for getting a loan by ID.
     *
     * @return string
     */
    public static function GetLoanById() {
        return '
            query GetLoanById($id: ID!) {
                GetLoanById(id: $id) {
                    id
                    transactions {
                        id
                    }
                }
            }
        ';
    }
}
