<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email verification code</title>
</head>

<body>
    <div
        style="display: flex;flex-direction: column; padding: 32px; justify-items: center; align-items: center; gap: 8px;">
        <img src="{{ config('app.url') }}:{{ env('APP_PORT', '80') }}/images/logo-money-master.png" alt=""
            style="width: 200px; height: 200px;">
        <p style="font-size: 30px; text-transform: uppercase;">Money Master</p>
        <div style="font-size: 20px;">Your email verification code is: <span
                style="font-size: 30px; font-weight: bold; color: purple">{{ $verificationCode }}</span></div>
    </div>

</body>

</html>
