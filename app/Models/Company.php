<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Company
 *
 * Model representing a company
 *
 * @package App
 */
class Company extends Model
{
    use UsesUuid;

    protected $table = "companies";

    /**
     * Branches owned by the company
     *
     * @return HasMany
     */
    public function branches(): HasMany {
        return $this->hasMany(CompanyBranch::class, 'company_id', 'id');
    }
}
