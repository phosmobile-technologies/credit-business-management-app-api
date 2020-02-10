<?php

namespace App\Listeners;

use App\Events\LoanApplicationStatusChanged;
use App\Models\enums\ActivityLogProperties;
use App\Models\Enums\LoanApplicationStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Activitylog\Models\Activity;

class LogLoanApplicationStatusChange
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
     * @param  LoanApplicationStatusChanged  $event
     * @return void
     */
    public function handle(LoanApplicationStatusChanged $event)
    {
        $loan = $event->loan;
        $oldApplicationStatus = $event->oldApplicationStatus;
        $causer = $event->causer;
        $message = $event->message;

        $description = "The Loan Application status was set to {$loan->application_status} by {$causer->last_name} {$causer->first_name}";
        $extraProperties = [ActivityLogProperties::ACTIVITY_DESCRIPTION => $description];
        if($message) {
            $extraProperties[ActivityLogProperties::ACTIVITY_MESSAGE] = $message;
        }

        activity()
            ->causedBy($causer)
            ->performedOn($loan)
            ->withProperties($extraProperties)
//            ->tap(function(Activity $activity) use ($description, $message) {
//                $activity[ActivityLogProperties::ACTIVITY_DESCRIPTION] = $description;
//                if($message) {
//                    $activity[ActivityLogProperties::ACTIVITY_MESSAGE] = $message;
//                }
//            })
            ->log('Loan application status updated');
    }
}
