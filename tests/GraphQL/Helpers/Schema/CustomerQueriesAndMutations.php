<?php

namespace Tests\GraphQL\Helpers\Schema;


class CustomerQueriesAndMutations
{
    /**
     * Query for getting a client by their id.
     *
     * @return string
     */
    public static function getClientById() {
        return '
            query GetCustomerById($id: ID!) {
                GetCustomerById(id: $id) {
                    id
                    loans {
                      id
                    }
                }
            }
        ';
    }

    /**
     * Query for getting a customer's loan transactions.
     *
     * @return string
     */
    public static function GetCustomerTransactionsById() {
        return '
            query GetCustomerTransactionsById(
            $customer_id: ID!, 
            $transaction_type: TransactionOwnerType!
            ) {
                GetCustomerTransactionsById(customer_id: $customer_id, transaction_type: $transaction_type) {
                    data {
                        id
                    }
                }
            }
        ';
    }
}
