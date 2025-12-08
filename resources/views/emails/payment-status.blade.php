<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Notification</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-wrapper {
            width: 100%;
            padding: 20px 0;
            background-color: #f4f4f7;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .email-header {
            text-align: center;
            padding-bottom: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a73e8;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-declined {
            color: #dc3545;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #eaeaea;
        }
        table th {
            background-color: #f9f9f9;
            width: 30%;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .footer a {
            color: #1a73e8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h1>{{ $payment->status == 2 ? 'Payment Successful' : 'Payment Declined' }}</h1>
            </div>

            <p>Dear Admin,</p>

            @if($payment->status == 2)
                <p class="status-paid">A payment has been successfully processed. Below are the details:</p>
            @else
                <p class="status-declined">A payment attempt has failed. Please review the details below:</p>
            @endif

            <table>
                <tr>
                    <th>Invoice ID</th>
                    <td>{{ $payment->unique_id }}</td>
                </tr>
                <tr>
                    <th>Client Name</th>
                    <td>{{ $payment->client->name }}</td>
                </tr>
                <tr>
                    <th>Package</th>
                    <td>{{ $payment->package }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $payment->description }}</td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>${{ number_format($payment->price, 2) }}</td>
                </tr>
                @if($payment->tax_amount > 0)
                <tr>
                    <th>Tax</th>
                    <td>${{ number_format($payment->tax_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <th>Status</th>
                    <td>{{ $payment->status == 2 ? 'Paid' : 'Declined' }}</td>
                </tr>
            </table>

            <p>Please review this transaction at your earliest convenience.</p>

            <div class="footer">
                &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
