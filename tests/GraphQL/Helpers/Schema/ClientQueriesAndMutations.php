<?php

namespace Tests\GraphQL\Helpers\Schema;


class ClientQueriesAndMutations
{
    /**
     * Query for getting a client by their id.
     *
     * @return string
     */
    public static function getClientById() {
        return '
            mutation GetClientById($id: ID!) {
                GetClientById(id: $id) {
                    id
                }
            }
        ';
    }
}
