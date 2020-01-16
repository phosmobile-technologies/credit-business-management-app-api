<?php

namespace App\Models\enums;

/**
 * Class TransactionMedium
 *
 * The various mediums through which a transaction can be done.
 *
 * @package App\Models\enums
 */
class TransactionMedium
{
    const CASH = "CASH";
    const BANK_TRANSFER = "BANK_TRANSFER";
    const BANK_TELLER = "BANK_TELLER";
    const ONLINE = "ONLINE";
}
