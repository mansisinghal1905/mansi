
<!DOCTYPE html>
<html>
<head>
    <title>Subscription Renewal Reminder</title>
</head>
<body>
    <h1>Dear {{ $subscription->name }},</h1>
    <p>Your subscription will expire on {{ \Carbon\Carbon::parse($subscription->subscription_end_date)->format('F d, Y') }}.</p>
    <p>Please renew your subscription to continue enjoying our services.</p>
    <p>Thank you!</p>
</body>
</html>
