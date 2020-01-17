<?php

namespace Tests\GraphQL\Helpers\Schema;


class BranchQueriesAndMutations
{
    public static function getBranchById() {
        return '
            query GetBranchById($id: ID!) {
                GetBranchById(id: $id) {
                    id
                    customers {
                        id
                    }
                }
            }
        ';
    }
}
