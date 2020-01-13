<?php

namespace App\Providers;

use App\Repositories\ContributionRepository;
use App\Repositories\Interfaces\ContributionRepositoryInterface;
use App\Repositories\Interfaces\LoanApplicationRepositoryInterface;
use App\Repositories\Interfaces\LoanRepositoryInterface;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LoanApplicationRepository;
use App\Repositories\LoanRepository;
use App\Repositories\UserProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();

        $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Make sure the directory for compiled views exist
         *
         * @for-serverless
         */
        if (! is_dir(config('view.compiled'))) {
            mkdir(config('view.compiled'), 0755, true);
        }
    }

    /**
     * Register our repositories with Laravel's IOC container
     *
     * @return void
     */
    private function registerRepositories() {
        $repositories = [
            UserRepositoryInterface::class => UserRepository::class,
            UserProfileRepositoryInterface::class => UserProfileRepository::class,
            LoanRepositoryInterface::class => LoanRepository::class,
            LoanApplicationRepositoryInterface::class => LoanApplicationRepository::class,
            ContributionRepositoryInterface::class => ContributionRepository::class,
        ];

        foreach($repositories as $interface => $repository) {
            $this->app->bind($interface, $repository);
        }
    }
}
