<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class MemberContribution extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "member_contributions";

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
}
