<?php

namespace Tests\GraphQL\Helpers\Schema;


class ContributionQueriesAndMutations
{
    /**
     * Mutation for creating a contribution
     *
     * @return string
     */
    public static function createContribution() {
        return '
            mutation CreateContribution($input: CreateContributionInput!) {
                CreateContribution(input: $input) {
                    id
                    contribution_type
                    contribution_amount
                    contribution_frequency
                }
            }
        ';
    }

    /**
     * Mutation for updating a contribution
     *
     * @return string
     */
    public static function updateContribution() {
        return '
            mutation UpdateContribution($input: UpdateContributionInput!) {
                UpdateContribution(input: $input) {
                    id
                    contribution_type
                    contribution_amount
                    contribution_frequency
                }
            }
        ';
    }

    /**
     * Mutation for deleting a contribution
     *
     * @return string
     */
    public static function deleteContribution() {
        return '
            mutation DeleteContribution($contribution_id: ID!) {
                DeleteContribution(contribution_id: $contribution_id) {
                    id
                    contribution_type
                    contribution_amount
                    contribution_frequency
                }
            }
        ';
    }

    /**
     * Mutation for funding a contribution plan from a wallet.
     *
     * @return string
     */
    public static function fundContributionPlanFromWallet() {
        return '
            mutation FundContributionPlanFromWallet($input: FundContributionPlanFromWalletInput!) {
                FundContributionPlanFromWallet(input: $input) {
                    id
                    contribution_type
                    contribution_amount
                    contribution_balance
                    contribution_frequency
                }
            }
        ';
    }
}
