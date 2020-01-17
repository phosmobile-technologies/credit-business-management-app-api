<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CompanyBranch extends Model
{
    use UsesUuid;

    protected $table = "company_branches";

    /**
     * The company that owns the branch.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function customers(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, UserProfile::class, 'branch_id');
    }

    public function loans(): HasManyThrough
    {
        return $this->hasManyThrough(Loan::class, UserProfile::class, 'branch_id', 'user_id', 'id', 'user_id');
    }
}
