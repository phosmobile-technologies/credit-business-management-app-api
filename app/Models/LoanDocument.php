<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDocument extends Model
{
    /**
     * @var string
     */
    protected $table = "loan_documents";

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
