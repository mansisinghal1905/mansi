<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-body {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background-color: #0044cc;
            color: #ffffff;
            padding: 10px 0;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333333;
        }
        .content h2 {
            margin-top: 0;
        }
        .ticket-details {
            margin-top: 20px;
            border-top: 1px solid #dddddd;
            padding-top: 20px;
        }
        .ticket-details p {
            margin: 5px 0;
            font-size: 16px;
        }
        .button {
            text-align: center;
            margin-top: 30px;
        }
        .button a {
            background-color: #0044cc;
            color: #ffffff;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            color: #888888;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #dddddd;
        }
        @media (max-width: 600px) {
            .email-body {
                padding: 10px;
            }
            .content, .ticket-details {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-body">
            <div class="header">
                <h1>Ticket Confirmation</h1>
            </div>
            <div class="content">
                <h2>Hello [{{ ucfirst($customer_name) }}],</h2>
                <p>Your ticket has been successfully generated. Please find the details of your ticket below:</p>
                
                <div class="ticket-details">
                    <p><strong>Ticket ID:</strong> {{$ticket_code}}</p>
                    <p><strong>Subject:</strong> {{$subject}}</p>
                    <p><strong>Priority:</strong> {{$priority}}</p>
                </div>

                <!-- <div class="button">
                    <a href="https://example.com/ticket/123456" target="_blank">View Ticket</a>
                </div> -->
            </div>

            <div class="footer">
                <p>&copy; 2024 Your Company. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
