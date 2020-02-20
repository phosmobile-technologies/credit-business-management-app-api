<?php

namespace Tests\GraphQL\Helpers\Schema;


class CustomerWithdrawalRequestQueriesAndMutations
{
    /**
     * Query for getting a customer withdrawal requests by their id.
     *
     * @return string
     */
    public static function getCustomerWithdrawalRequestById()
    {
        return '
            query GetCustomerWithdrawalRequestById($id: ID!) {
                    request_amount
                    request_balance 
                    request_status
                    request_type
            }
        ';
    }

    /**
     * Mutation for creating a CustomerWithdrawalRequest
     *
     * @return string
     */
    public static function createCustomerWithdrawalRequest()
    {
        return '
            mutation CreateCustomerWithdrawalRequest($input: CreateCustomerWithdrawalRequestInput!) {
                CreateCustomerWithdrawalRequest(input: $input) {
                    user_id
                    request_amount
                    request_balance 
                    request_status
                    request_type 
                }
            }
        ';
    }

    /**
     * Mutation for updating a CustomerWithdrawalRequest
     *
     * @return string
     */
    public static function updateCustomerWithdrawalRequest()
    {
        return '
            mutation UpdateCustomerWithdrawalRequest($input: UpdateCustomerWithdrawalRequestInput!) {
                UpdateCustomerWithdrawalRequest(input: $input) {
                    id
                    request_amount
                    request_balance 
                    request_status
                    request_type 
                }
            }
        ';
    }

    /**
     * Mutation for deleting a CustomerWithdrawalRequest
     *
     * @return string
     */
    public static function deleteCustomerWithdrawalRequest()
    {
        return '
            mutation DeleteCustomerWithdrawalRequest($user_id: ID!) {
                DeleteCustomerWithdrawalRequest(user_id: $id) {
                    id
                    request_type
                    request_amount
                    request_status
                }
            }
        ';
    }
}
