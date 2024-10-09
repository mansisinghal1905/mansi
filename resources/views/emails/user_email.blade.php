<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        h1 {
            font-size: 24px;
            color: #2c3e50;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        strong {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <h1>Welcome, {{ ucfirst($name) }}</h1>
    <p>Your account has been created successfully. Below are your login details:</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
</body>
</html>