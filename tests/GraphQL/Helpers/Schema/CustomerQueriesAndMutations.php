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
     * Query for getting a customer's transactions.
     *
     * @return string
     */
    public static function GetCustomerTransactionsById() {
        return '
            query GetCustomerTransactionsById($customer_id: ID!) {
                GetCustomerTransactionsById(customer_id: $customer_id) {
                    data {
                        id
                    }
                }
            }
        ';
    }
}
