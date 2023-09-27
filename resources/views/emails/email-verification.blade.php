<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email verification code</title>
</head>

<body>
    <div>
        <img src="{{ env('APP_URL') }}/images/logo-money-master.png" alt="" style="width: 200px; height: 200px;">
    </div>
    <p style="font-size: 30px; text-transform: uppercase;">Money Master</p>
    <div style="font-size: 20px;">Your email verification code is: <span
            style="font-size: 30px; font-weight: bold; color: purple">{{ $verificationCode }}</span></div>

</body>

</html>
