<?php

namespace App\Listeners;

use App\Events\RequestStatusChanged;
use App\Models\enums\ActivityLogProperties;
use App\Models\Enums\RequestStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Activitylog\Models\Activity;

class LogRequestStatusChange
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
     * @param  RequestStatusChanged  $event
     * @return void
     */
    public function handle(RequestStatusChanged $event)
    {
        $customerwithdrawalrequest = $event->customerwithdrawalrequest;
        $oldRequestStatus = $event->oldRequestStatus;
        $causer = $event->causer;
        $message = $event->message;

        $description = "The Request status was set to {$customerwithdrawalrequest->request_status} by {$causer->last_name} {$causer->first_name}";
        $extraProperties = [ActivityLogProperties::ACTIVITY_DESCRIPTION => $description];
        if ($message) {
            $extraProperties[ActivityLogProperties::ACTIVITY_MESSAGE] = $message;
        }

        activity()
            ->causedBy($causer)
            ->performedOn($customerwithdrawalrequest)
            ->withProperties($extraProperties)
            ->log('Request status updated');
    }
}
