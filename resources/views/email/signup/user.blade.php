@component('mail::message')

    @component('mail::panel')
        Hello **{{ $user["first_name"] }} {{ $user["last_name"] }},**

        You have successfully been registered on the SpringVerse platform.

        @if($registration_source == \App\Models\enums\RegistrationSource::BACKEND)
            {
            Please find your login credentials below:

            * Email - {{ $user['email'] }}
            * Default Password - {{ $defaultPassword }}
            }
        @endif

        <a href="{{env('APP_URL')}}/login">Login</a>
        If you are not a Springverse customer, please disregard this email.
        please disregard this email.

        Welcome on board,<br>
        Springverse Team

    @endcomponent

@endcomponent
