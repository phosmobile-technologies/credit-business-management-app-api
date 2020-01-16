<?php

namespace App\Models\enums;

/**
 * Class TransactionStatus
 *
 * The possible statuses of a transaction
 *
 * @package App\Models\enums
 */
class TransactionStatus
{
    const COMPLETED = "COMPLETED";
    const PENDING = "PENDING";
    const FAILED = "FAILED";
}
