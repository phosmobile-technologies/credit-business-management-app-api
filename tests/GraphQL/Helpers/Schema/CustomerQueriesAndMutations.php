<?php

namespace Tests\GraphQL\Helpers\Schema;


class CustomerQueriesAndMutations
{
    /**
     * Query for getting a client by their id.
     *
     * @return string
     */
    public static function getClientById()
    {
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
     * Query for getting a customer's loan transaction.
     *
     * @return string
     */
    public static function GetCustomerTransactionsById()
    {
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

    /**
     * Query for getting a customer's loan and contribution plan statistics.
     *
     * @return string
     */
    public static function GetCustomerStatisticsQuery()
    {
        return '
              query GetCustomerStatistics($customer_id: ID!) {
                GetCustomerStatistics(customer_id: $customer_id) {
                          loan_statistics {
                          loan_balance
                          next_due_payment
                          next_repayment_date
                          default_charges
                          total_paid_amount
                          active_loan
                        }
                        
                        contribution_plan_statistics {
                         total_contribution_sum
                          goal_contribution_sum
                          fixed_contribution_sum
                          locked_contribution_sum
                          wallet_balance
                        }
                    }   
              }
        ';
    }
}
