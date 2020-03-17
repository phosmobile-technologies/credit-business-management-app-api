<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class ContributionPlan extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "contribution_plans";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Make the contribution_id into a custom readable format.
     *
     * @return string
     */
    public function getContributionIdAttribute() {
        return "Loan - {$this->contribution_id}";
    }

    /**
     * Get all of the contribution plan's transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function transactions() {
        return $this->morphMany(Transaction::class, 'owner');
    }
}
