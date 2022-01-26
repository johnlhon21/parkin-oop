<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width" />
</head>
<body>
<table style="width: 100%;background-color: #f7f7f7;padding:0;">
    <tr>
        <td style="padding:30px 0;">
            <table id="th-ticket-template" style="margin: 0 auto;
				border-collapse: collapse;
				font-family: 'roboto', 'helvetica neue','helvetica','arial','sans-serif';
				color: #333;
				width: 640px;
				table-layout: fixed;">
                <tr style="background-color: #fff;">
                    <td colspan='4' style="padding: 24px 24px;font-weight: bold;font-size: 22px;">
                        <span>Basket Password Reset</span>
                    </td>
                </tr>
                <tr style="background-color: #fff;">
                    <td colspan='4' style="padding: 0px 24px;font-size: 13px;line-height: 24px;">
                        <p>
                            <span>Hi {{ $user->first_name }} {{ $user->last_name }},</span>
                            <br>
                            We received a request to reset your Basket Console password.
                            Enter the following password reset code:
                            <br><br>
                        <span style="border: solid 1px #8c8c8c; padding: 5px; font-weight: bold; font-size: 24px">{{ $code }}</span>
                        </p>
                    </td>
                </tr>
                <tr style="background-color: #fff;">
                    <td colspan='4' style="padding: 12px 24px 32px;">
                        <p style="color:gray;font-style: italic;font-size:12px;">This is a system-generated email.</p>
                    </td>
                </tr>
                <tr style=""><td colspan="4">&nbsp;</td></tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
