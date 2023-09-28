<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet" />
    <title>Password reset link</title>
</head>

<body>

    <div style="">
        <table
            style="margin-left: auto; margin-right: auto; width: 70%; background-color: #F5EDFF; border-radius: 30px; padding-top: 32px; padding-bottom: 32px;">
            <thead>
                <tr>
                    <th>
                        <img src="{{ config('app.url') }}/images/logo-money-master.png" alt=""
                            style="width: 200px; height: 200px;">
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p style="font-size: 30px; text-transform: uppercase;text-align: center;">Money Master</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p style="font-size: 20px; text-align: center;">Your pasword reset link is: <a
                                style="font-size: 20; color: purple; font-weight: bold"
                                href="{{ $link }}">{{ $link }}</a>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>


    </div>
</body>

</html>
