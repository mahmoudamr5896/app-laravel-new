<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
</head>
<body>
    <h1>Payment Successful</h1>
    <p>Dear User,</p>
    <p>Your payment of ${{ number_format($amount, 2) }} using {{ $payment_method }} has been successfully processed.</p>
    <p>Status: {{ ucfirst($status) }}</p>
    <p>Transaction ID: {{ $transaction_id }}</p>
    <p>Thank you for your purchase!</p>
</body>
</html>
