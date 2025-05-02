<!-- resources/views/emails/verify_email.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>Thank you for registering. Please click the link below to verify your email:</p>
    <a href="{{ url('/api/verify-email?token=' . $verificationToken) }}">Verify Email</a>
</body>
</html>
