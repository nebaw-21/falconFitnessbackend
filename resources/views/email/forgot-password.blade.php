<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <p>Hello,</p>
    
    <p>You are receiving this email because we received a password reset request for your account.</p>
    
    <p>
        If you did not request a password reset, no further action is required.
    </p>
    
    <p>
        To reset your password, click on the following link:
        <a href="http://localhost:3000/reset/{{ $token }}">Reset Password</a>
    </p>
    
    <p>
        This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes.
    </p>
    
    <p>
        If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
        <br>
        <a href="http://localhost:3000/reset/{{ $token }}">http://localhost:3000/reset?token={{ $token }}</a>
    </p>
    
    <p>Thank you!</p>
</body>
</html>