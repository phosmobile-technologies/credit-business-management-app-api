<?php

namespace App\Models\enums;

/**
 * Class TransactionOwnerType
 *
 * The type of model that owns a transaction.
 * Check AppServiceProvider@boot for the MorphMap which maps these types to FQDN model paths.
 *
 * @package App\Models\enums
 */
class TransactionOwnerType
{
    const LOAN = "LOAN";
    const CONTRIBUTION_PLAN = "CONTRIBUTION_PLAN";
}
