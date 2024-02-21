<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body>
    <h2>Password Reset</h2>

    <p>Hi {{ $user->name }},</p>

    <p>You are receiving this email because we received a password reset request for your account.</p>

    <p>Click the following link to reset your password:</p>

    <p><a href="{{ $resetUrl }}">Reset Password</a></p>

    <p>If you didn't request a password reset, no further action is required.</p>

    <p>Regards,<br>
    Your Application</p>
</body>
</html>