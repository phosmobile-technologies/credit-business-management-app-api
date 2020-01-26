<?php

namespace App\Models\enums;

/**
 * Class TransactionType
 *
 * Transaction types in the application
 *
 * @package App\Models\enums
 */
class TransactionType
{
    const LOAN_REPAYMENT = "LOAN_REPAYMENT";
    const CONTRIBUTION_PAYMENT = "CONTRIBUTION_PAYMENT";

    const CONTRIBUTION_WITHDRAWAL = "CONTRIBUTION_WITHDRAWAL";
    const DEFAULT_REPAYMENT = "DEFAULT_REPAYMENT";
    const DEFAULT_CANCELLATION = "DEFAULT_CANCELLATION";
    const LOAN_DISBURSEMENT = "LOAN_DISBURSEMENT";
    const VENDOR_PAYOUT = "VENDOR_PAYOUT";
    const BRANCH_EXPENSE = "BRANCH_EXPENSE";
    const BRANCH_FUND_DISBURSEMENT = "BRANCH_FUND_DISBURSEMENT";
}
