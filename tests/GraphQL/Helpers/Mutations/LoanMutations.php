<?php

namespace Tests\GraphQL\Helpers\Mutations;


class LoanMutations
{
    public static function CreateLoanMutation() {
        return '
            mutation CreateLoan($input: CreateLoanInput!) {
                CreateLoan(input: $input) {
                    loan_amount
                }
            }
        ';
    }
}
