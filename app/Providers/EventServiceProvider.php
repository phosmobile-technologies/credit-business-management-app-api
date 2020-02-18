<?php

namespace App\Providers;

use App\Events\NewCustomerWithdrawalRequestCreated;
use App\Events\RequestStatusChanged;
use App\Events\LoanApplicationStatusChanged;
use App\Events\NewLoanCreated;
use App\Events\NewUserRegistered;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        NewUserRegistered::class => [
            "App\Listeners\SendUserRegistrationEmail"
        ],

        NewLoanCreated::class => [
            "App\Listeners\SendLoanCreatedEmail"
        ],

        NewCustomerWithdrawalRequestCreated::class => [
            "App\Listeners\SendCustomerWithdrawalRequestCreatedEmail"
        ],

        LoanApplicationStatusChanged::class => [
            "App\Listeners\LogLoanApplicationStatusChange"
        ],

        RequestStatusChanged::class => [
            "App\Listeners\LogRequestStatusChange"
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
