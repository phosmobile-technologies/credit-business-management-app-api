<?php

namespace Tests\GraphQL\Helpers\Schema;


class WalletQueriesAndMutations
{
    /**
     * Mutation for creating a wallet
     *
     * @return string
     */
    public static function createWallet()
    {
        return '
            mutation Createwallet($input: CreateWalletInput!) {
                CreateWallet(input: $input) {
                    wallet_balance
                }
            }
        ';
    }

    /**
     * Mutation for updating a wallet
     *
     * @return string
     */
    public static function updateWallet()
    {
        return '
            mutation UpdateWallet($input: UpdateWalletInput!) {
                UpdateWallet(input: $input) {
                    id
                    wallet_balance
                }
            }
        ';
    }

    /**
     * Query for getting a wallet by ID.
     *
     * @return string
     */
    public static function GetWalletById()
    {
        return '
            query GetWalletById($id: ID!) {
                GetWalletById(id: $id) {
                    id
                    user_id
                    wallet_id
                    wallet_balance
                    transactions {
                        id
                        transaction_type
                    }
                }
            }
        ';
    }
}
