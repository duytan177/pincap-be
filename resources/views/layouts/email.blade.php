<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
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
            color: #fff;
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
                <p>Hi <b>{{ $user->name }}</b>,</p>
                <p>Thank you for registering with us22.</p>
                <p>Please click the button below to verify your email address:</p>
                <a href="{{ url(config('common.path_verify_email') . $user->verification_token) }}"
                    class="verify-button">Verify Email</a>
                <p>This link will expire in 5 minutes.</p>
                <p>If you didn't request this, you can ignore this email.</p>
                <p>Best regards,</p>
                <p>NFT marketplace</p>
            </div>
        </div>
    </div>
</body>

</html>
