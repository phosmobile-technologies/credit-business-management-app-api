<?php

namespace App;

use App\Models\Concerns\UsesUuid;
use App\Models\ContributionPlan;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\UserProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, UsesUuid, LogsActivity, HasRoles, CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name', 'phone_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Find the user instance for the given username.
     * This method is used by Laravel Passport to determine how to resolve the username during authentication.
     *
     * @param  string $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)
            ->orWhere('phone_number', $username)
            ->first();
    }

    /**
     * Encrypt the user password when setting it.
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Relationship between a user and their user profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    /**
     * Relationship between a user and their loans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function loanTransactions() {
        return $this->hasManyThrough(Transaction::class, Loan::class, 'user_id', 'owner_id', 'id', 'id');
    }

    /**
     * Relationship between a user and their contribution plans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributionPlans(): HasMany
    {
        return $this->hasMany(ContributionPlan::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function contributionPlansTransactions() {
        return $this->hasManyThrough(Transaction::class, ContributionPlan::class, 'user_id', 'owner_id', 'id', 'id');
    }

}
