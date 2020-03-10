<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessedTransaction
 *
 * Model representing transactions that have been processed i.e approved or disapproved.
 * This model serves to store who processed a transaction, and what message they left (if any)
 *
 * @package App\Models
 */
class ProcessedTransaction extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "processed_transactions";

    /**
     * @var array
     */
    protected $guarded = [];
}
