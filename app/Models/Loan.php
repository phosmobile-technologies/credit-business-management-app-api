<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\Models\Enums\LoanDefaultStatus;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "loans";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the total loan default amount accrued for a defaulting loan.
     */
    public function getTotalDefaultAmountAttribute()
    {
        if($this->loan_default_status === LoanDefaultStatus::NOT_DEFAULTING) {
            return 0;
        }

        return $this->num_of_default_days * $this->default_amount;
    }
}
