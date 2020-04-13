@component('mail::message')

    @component('mail::panel')
        **Hello,**

        Someone requested a password reset link for this account. If it was not you, kindly disregard this mail.
        If you would like to continue, please click the link below.

        @component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
            Reset Your Password.
        @endcomponent

        Thanks,<br>
        Credit Business Management App

    @endcomponent

@endcomponent
