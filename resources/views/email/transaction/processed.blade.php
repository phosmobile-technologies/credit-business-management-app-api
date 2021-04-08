@component('mail::message')

    @component('mail::panel')
        Dear **{{ $user["first_name"] }} {{ $user["last_name"] }},**

        A transaction has been processed on your umc account.

        Please see the transaction details below:

            * Transaction Type - {{ $transaction->transaction_type }}
            * Transaction Amount - {{ $transaction->transaction_amount }}
            * Transaction Message - {{ $processedTransaction->message }}
            * Transaction Date - {{ $transaction->transaction_date }}

        If you are not a UMC customer, please disregard this email.

        Warm Regards,
        The UMC Team

    @endcomponent

@endcomponent