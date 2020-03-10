<?php

namespace App\Listeners;

use App\Events\NewLoanCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanCreatedEmail
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
     * @param  NewLoanCreated  $event
     * @return void
     */
    public function handle(NewLoanCreated $event)
    {
        //
    }
}
