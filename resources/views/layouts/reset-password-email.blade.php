<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset passsword</title>
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-content {
            font-family: Arial, sans-serif;
            color: #000;
        }

        .verify-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 30%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-content">
                <h2>Reset Your Password</h2>
                <p>Hi <b>{{ $name }}</b>,</p>
                <p>You recently requested to reset your password. To reset your password, please click the button below:
                </p>
                <a href="{{ config('app.url') . config('common.path_forgot_password') . $token }}"
                    class="verify-button">Reset
                    Password</a>
                <p>This link will expire in 60 minutes.</p>
                <p>If you didn't request this, you can ignore this email.</p>
                <p>Best regards,</p>
                <p>NFT marketplace</p>
            </div>
        </div>
    </div>
</body>

</html>
