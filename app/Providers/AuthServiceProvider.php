<?php

namespace App\Providers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\ContributionPlan;
use App\Models\Transaction;
use App\Policies\LoanApplicationPolicy;
use App\Policies\LoanPolicy;
use App\Policies\ContributionPlanPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Loan::class => LoanPolicy::class,
        LoanApplication::class => LoanApplicationPolicy::class,
        ContributionPlan::class => ContributionPlanPolicy::class,
        Transaction::class => TransactionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
