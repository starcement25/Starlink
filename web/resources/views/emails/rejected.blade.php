<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Summary</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">

<table cellpadding="0" cellspacing="0" width="100%" style="background:#f4f6f8; padding:20px 0;">
    <tr>
        <td align="center">
            <table width="680" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

                {{-- HEADER --}}
                <tr>
                    <td style="
                        padding:30px 20px;
                        text-align:center;
                        background:red;
                    ">
                        <img src="{{ url('https://dev.starlinkinfluencers.in/web/public/logo/star-login.jpg') }}"
                             alt="Logo"
                             style="height:90px; width:auto; display:block; margin:0 auto;">

                        <div style="
                            font-size:22px;
                            font-weight:700;
                            color:#ffffff;
                            margin-top:12px;
                            ">
                            Order Rejection
                        </div>
                    </td>
                </tr>

                {{-- CONTENT --}}
                <tr>
                    <td style="padding:24px; font-size:15px; color:#374151; line-height:1.6;">

                        <p style="font-size:16px; margin:0 0 10px;"><strong>Hello Team,</strong></p>
                        <p style="margin:0 0 20px;">
                           The following order has been rejected. Details are provided below.
                        </p>

                      
                        <table width="100%" cellpadding="8" cellspacing="0" 
                            style="border-collapse: collapse; margin-top:10px; background:#fafafa;">

                            <tr>
                                <td style="border:1px solid #ddd; font-weight:bold; width:35%;">Client Name</td>
                                <td style="border:1px solid #ddd;">{{ $user }}</td>
                            </tr>

                            <tr>
                                <td style="border:1px solid #ddd; font-weight:bold;">Product Name</td>
                                <td style="border:1px solid #ddd;">{{ $product }}</td>
                            </tr>

                            <tr>
                                <td style="border:1px solid #ddd; font-weight:bold;">Order ID</td>
                                <td style="border:1px solid #ddd;">{{ $orderId ?? "" }}</td>
                            </tr>

                            <tr>
                                <td style="border:1px solid #ddd; font-weight:bold;">Status</td>
                                <td style="border:1px solid #ddd;"> Rejected</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #ddd; font-weight:bold;">Reason</td>
                                <td style="border:1px solid #ddd;"> {{ $remarks }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #ddd; font-weight:bold;">Performed By</td>
                                <td style="border:1px solid #ddd;"> {{ $adminUser }}</td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;">
                            Thank you.<br>
                            <strong>StarLink System</strong>
                        </p>

                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td style="
                        padding:16px 24px;
                        font-size:13px;
                        background:red;
                        text-align:center;
                        color:#ffffff;
                    ">
                        © StarLink. All rights reserved.<br>
                        <small>If you believe you received this message in error, please contact us.</small>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>