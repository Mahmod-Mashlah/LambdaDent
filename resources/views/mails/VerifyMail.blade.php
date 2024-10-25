<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify E-mail || Lambda Dent </title>
</head>

<body>

    <h3> Dear Client,</h3>

    <p>We received a request to access your Google Account {{ $email }} through your email address.
    </p>
    <p>
        Your verification code is:
    </p>
    <h2><b style="color: rgb(7, 121, 220);">
            <pre>{{ $code }}</pre>
        </b></h2>

    <p>If you did not request this code, it is possible that someone else is trying to
    </p>
    <p>make an Account but he write his email wrongly,so please ignore this email.
    </p>
    <h4>
        Sincerely yours,
    </h4>
    <h3>
        {{ env('APP_NAME') }} team
    </h3>
</body>

</html>
