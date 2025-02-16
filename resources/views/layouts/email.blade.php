<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4; width: 100%; height: 100%; text-align: center;">
        <tr>
            <td align="center">
                <table role="presentation" width="500px" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center;">
                    <tr>
                        <td style="font-family: Arial, sans-serif; color: #333;">
                            <h2>Welcome to PINCAP!</h2>
                            <p>Hi <b>{{ $user->name }}</b>,</p>
                            <p>Thank you for joining <b>PINCAP</b>, the platform for sharing and discovering amazing media content.</p>
                            <p>Please click the button below to verify your email address and start exploring:</p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td style="border-radius: 5px; background-color: #007bff; text-align: center;">
                                        <a href="{{ config('app.url') . config('common.path_verify_email') . $user->verification_token }}"
                                           style="display: inline-block; padding: 12px 20px; color: #fff; text-decoration: none; font-weight: bold; font-family: Arial, sans-serif;">Verify Email</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-size: 12px; color: #777; margin-top: 15px;">This link will expire in 5 minutes. If you didn't request this, please ignore this email.</p>
                            <p style="font-size: 12px; color: #777;">Best regards, <br> The PINCAP Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
