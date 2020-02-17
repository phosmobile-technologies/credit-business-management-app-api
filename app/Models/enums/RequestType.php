<?php

namespace App\Models\Enums;

/**
 * Class RequestType
 *
 * Status stipulating the type of the withdrawal request a customer can nmake.
 *
 * @package App\Models\Enums
 */
class RequestType 
{
    const BRANCH_FUND = "BRANCH_FUND";
    const BRANCH_EXTRA_FUND = "BRANCH_EXTRA_FUND";
    const DEFAULT_CANCELLATION = "DEFAULT_CANCELLATION";
    const VENDOR_PAYOUT = "VENDOR_PAYOUT";
    const CONTRIBUTION_WITHDRAWAL  = "CONTRIBUTION_WITHDRAWAL";
}