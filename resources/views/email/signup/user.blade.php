@component('mail::message')

    @component('mail::panel')
        Hello **{{ $user["first_name"] }} {{ $user["last_name"] }},**

        You have successfully been registered on the SpringVerse platform.
        Please confirm your email address by clicking the button below.


        If you are not a Springverse customer, please disregard this email.
        please disregard this email.

        Welcome on board,<br>
        Springverse Team

    @endcomponent

@endcomponent
