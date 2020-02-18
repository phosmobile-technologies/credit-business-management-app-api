<?php

namespace App\Models\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class RequestStatus
 *
 * Status stipulating the status of the withdrawal request by customers.
 *
 * @package App\Models\Enums
 */
class RequestStatus extends Enum
{
    const APPROVED_BY_BRANCH_MANAGER = "APPROVED_BY_BRANCH_MANAGER";
    const DISAPPROVED_BY_BRANCH_MANAGER = "DISAPPROVED_BY_BRANCH_MANAGER";
    const DISAPPROVED_BY_GLOBAL_MANAGER = "DISAPPROVED_BY_GLOBAL_MANAGER";
    const APPROVED_BY_GLOBAL_MANAGER = "APPROVED_BY_GLOBAL_MANAGER";
    const PENDING = "PENDING";
    const DISBURSED = "DISBURSED";
}
