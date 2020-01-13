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
}
