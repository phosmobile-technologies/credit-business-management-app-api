<?php

namespace App\Models\Enums;


class LoanConditionStatus
{
    const ACTIVE = "ACTIVE";
    const INACTIVE = "INACTIVE"; # Loan has default of up to 90 days
    const COMPLETED = "COMPLETED";
    const NONPERFORMING = "NONPERFORMING"; # Loan has default of more than 30 days
}
