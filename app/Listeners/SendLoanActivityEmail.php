<?php

namespace App\Listeners;

use App\Events\NewLoanActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanActivityEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewLoanActivity  $event
     * @return void
     */
    public function handle(NewLoanActivity $event)
    {
        //
    }
}
