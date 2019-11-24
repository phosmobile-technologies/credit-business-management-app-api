<?php

namespace App\Providers;

use App\Repositories\Interfaces\UserRepositoryInterface;
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
            UserRepositoryInterface::class => UserRepository::class
        ];

        foreach($repositories as $interface => $repository) {
            $this->app->bind($interface, $repository);
        }
    }
}
