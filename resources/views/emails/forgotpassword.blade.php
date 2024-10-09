
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laravel</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
</head>
<body style="background-color:#fff; font-family:Arial, sans-serif; font-size:14px;">
    <center>
        <table width="800" cellspacing="0" style="border:1px solid #efefef; border-radius:5px; overflow:hidden;">
            <tr>
                <td bgcolor="#516672" style="padding:10px 20px;">
                    <table style="width:100%;">
                        <tr>
                            <td>
                                <div style="color:#fff; display:block; margin:0 auto; width:100%; text-align:center;">
                                    <!-- <img src="{{ asset('images/box_logo_trans.jpg') }}" alt="homepage" class="dark-logo" style="width:50px;"> -->
                                    <div>Alphabet</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; padding:40px 50px 10px; border-bottom:1px solid #e1e1e1;" bgcolor="#f9f9f9">
                    <!-- <p style="font-weight:bold; font-size:18px; margin-top:60px;">Dear, ,</p> -->
                    <p style="font-size:16px; color:#5f5e5e; line-height:1.5; margin-top:10px;">
                        If you've lost your password or wish to reset it,<br>
                        use the link below to get started.
                    </p>
                    <p style="margin-top:20px;">
                        <a href="{{ route('reset.password.get', $token) }}" style="display:inline-block; padding:10px 20px; font-size:16px; color:#fff; background-color:#007bff; text-decoration:none; border-radius:5px;">Reset Password</a>
                    </p>
                    <p style="font-size:16px; color:#5f5e5e; line-height:1.5; margin-top:20px;">
                        If you did not request a password reset, please ignore this email.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="text-align:center; padding:20px 10px; font-size:13px; color:#666;">
                    Copyright @ {{ date('Y') }} Laravel. All rights reserved.
                </td>
            </tr>
        </table>
    </center>
</body>
</html>

