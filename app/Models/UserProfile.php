<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use UsesUuid;

    /**
     * @var string
     */
    protected $table = "user_profile";
}
