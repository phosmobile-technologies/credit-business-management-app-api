<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "transactions";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the owing model of the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }
}
