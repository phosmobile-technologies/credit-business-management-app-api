<div style="width: 100%; background-color: white; padding: 5%">
    <h5>Dear {{ $data["name"] }},</h5>
    <br>
    <div style="width: 80%;">
       <p>
           You have been invited to join UMC Upspring Multipurpose Cooperative by {{ $data['user']->first_name . " ". $data['user']->last_name}}
           Upspring is an investment platform where you can save money with good returns and apply for loans with the cheapest interest rates
       </p>
    </div>
    <br>
    <a href="{{$redirectUrl}}" style="box-sizing: border-box; background-color: #003aba; padding: 14px 28px 14px 28px; border-radius: 3px; line-height: 18px!important; letter-spacing: 0.125em; text-transform: uppercase; font-size: 13px; font-family: 'Open Sans',Arial,sans-serif; font-weight: 400; color: #ffffff; text-decoration: none; display: inline-block;">
        Join Here
    </a>
    <br>
    <br>
    UMC Team
</div>
