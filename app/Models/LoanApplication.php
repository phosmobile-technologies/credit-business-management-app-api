<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class LoanApplication
 *
 * An application made by a customer to receive a loan from the company
 *
 * @package App
 */
class LoanApplication extends Model
{
    use UsesUuid, LogsActivity;

    protected $table = "loan_applications";

    protected $guarded = [];

    /**
     * The user who made the Loan Application.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * The admin staff to handle the Loan Application.
     *
     * @return BelongsTo
     */
    public function assignedTo(): BelongsTo {
        return $this->belongsTo(User::class, 'assignee_id', 'id');
    }

    /**
     * The branch manager who assigned Loan Application to an admin staff.
     *
     * @return BelongsTo
     */
    public function assignedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    /**
     * Get all of the loan application's documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documents()
    {
        return $this->morphMany(LoanDocument::class, 'owner');
    }

    /**
     * Determine if the loan application has been assigned to an admin staff for processing
     *
     * @return bool
     */
    public function getIsAssignedAttribute(): bool {
        return !is_null($this->assignee_id);
    }
}
