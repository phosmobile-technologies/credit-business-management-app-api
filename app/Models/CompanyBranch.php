<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBranch extends Model
{
    use UsesUuid;

    protected $table = "company_branches";

    /**
     * The company that owns the branch.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function customers() {
        return $this->hasManyThrough(User::class, UserProfile::class, 'branch_id');
    }
}
