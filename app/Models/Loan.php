<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\Models\Enums\LoanDefaultStatus;
use App\User;
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
     * Get all of the loan's transaction.
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
