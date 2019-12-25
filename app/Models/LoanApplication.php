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
}
