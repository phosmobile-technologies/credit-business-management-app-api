@component('mail::message')

    @component('mail::panel')
        Dear **{{ $user["first_name"] }} {{ $user["last_name"] }},**

        A transaction has been processed on your umc account.

        Please see the transaction details below:


            * Transaction Type - {{ $user['email'] }}
            * Transaction Amount - {{ $defaultPassword }}
            * Transaction Message - {{ $defaultPassword }}
            * Transaction Date - {{ $defaultPassword }}

        If you are not a UMC customer, please disregard this email.
        please disregard this email.

        Warm Regards,<br>
        The UMC Team

    @endcomponent

@endcomponent
