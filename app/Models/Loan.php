<?php

namespace App\Models;

use App\GraphQL\Errors\GraphqlError;
use App\Models\Concerns\UsesUuid;
use App\Models\Enums\LoanConditionStatus;
use App\Models\Enums\LoanDefaultStatus;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

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

    /**
     * Determine if the due date for the loan has been reached
     */
    public function dueDateReached() {
        if (!isset($this->due_date)) {
            return false;
        }

        $today = Carbon::today();
        return $today->diffInDays($this->due_date, false) <= 0;
    }

    /**
     * Get the amount of months left for the loan
     * @throws GraphqlError
     */
    public function getMonthsLeftAttribute()
    {
        if($this->loan_condition_status === LoanConditionStatus::INACTIVE ||
            $this->loan_condition_status === LoanConditionStatus::COMPLETED) {
            return null;
        }

        if($this->dueDateReached()) {
            return 0;
        }else {
            if(isset($this->due_date)) {
                $today = Carbon::today();
                return $today->diffInMonths($this->due_date, false);
            } else {
                throw new GraphqlError("Unable to calculate number of months left on loan {$this->loan_identifier}");
            }

        }
    }

    /**
     * Gets the total interest amount for a loan.
     *
     * A special calculation other than (amount_disbursed * interest_rate_in_percent) is needed because Springverse uses the reducing balance method.
     */
    public function getTotalInterestAmountAttribute()
    {
        $monthlyPrincipalRepayment = $this->amount_disbursed / $this->tenure;
        $reducingBalance = $this->amount_disbursed + $monthlyPrincipalRepayment;
        $cumulativeInterest = 0 ;
        $interestRate = $this->interest_rate / 100;

        for($i = 0; $i < $this->tenure; $i++) {
            $reducingBalance -= $monthlyPrincipalRepayment;
            $cumulativeInterest += ($reducingBalance * $interestRate);
        }

        return round($cumulativeInterest, 2);
    }

    /**
     * Get the next due amount to be paid for a loan.
     */
    public function getNextDueAmountAttribute()
    {
        $totalLoanBalance = $this->loan_balance + $this->totalDefaultAmount;
        return ($totalLoanBalance) / $this->monthsLeft;
    }

    /**
     * Get all of the loan's transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function transactions() {
        return $this->morphMany(Transaction::class, 'owner');
    }

    /**
     * The user who owns the loan.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Loan Activities
     *
     * @return HasMany
     */
    public function activities(): HasMany {
        return $this->hasMany(Activity::class, 'subject_id', 'id');
    }

}
