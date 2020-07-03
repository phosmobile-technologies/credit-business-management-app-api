@component('mail::message')

    @component('mail::panel')
        Hello **{{ $user["first_name"] }} {{ $user["last_name"] }},**

        You have successfully been registered on the UMC platform.

        @if($registration_source == \App\Models\enums\RegistrationSource::BACKEND)
            {
            Please find your login credentials below:

            * Email - {{ $user['email'] }}
            * Default Password - {{ $defaultPassword }}
            }
        @endif

        @component('mail::button', ['url' => $loginUrl])
            Log in to your account
        @endcomponent

        If you are not a Springverse customer, please disregard this email.
        please disregard this email.

        Welcome on board,<br>
        UMC Team

    @endcomponent

@endcomponent
