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
                }
            }
        ';
    }
}
