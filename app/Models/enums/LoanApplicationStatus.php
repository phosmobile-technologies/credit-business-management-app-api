<?php

namespace App\Models\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class LoanApplicationStatus
 *
 * The status of a loan application.
 *
 * @package App\Models\Enums
 */
class LoanApplicationStatus extends Enum
{
    const APPROVED_BY_BRANCH_MANAGER = "APPROVED_BY_BRANCH_MANAGER";
    const APPROVED_BY_GLOBAL_MANAGER = "APPROVED_BY_GLOBAL_MANAGER";
    const DISAPPROVED_BY_BRANCH_MANAGER = "DISAPPROVED_BY_BRANCH_MANAGER";
    const DISAPPROVED_BY_GLOBAL_MANAGER = "DISAPPROVED_BY_GLOBAL_MANAGER";
    const PENDING = "PENDING";
}
