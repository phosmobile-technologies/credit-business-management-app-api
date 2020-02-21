<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class CustomerWithdrawalRequest
 *
 * A request made by a customer to withdraw an amount from the company
 *
 * @package App
 */

class CustomerWithdrawalRequest extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "customer_withdrawal_requests";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * The user who made the Withdrawal Request.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
