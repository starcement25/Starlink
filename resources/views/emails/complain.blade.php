<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarLink</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">


<table cellpadding="0" cellspacing="0" width="100%" style="background:#f4f6f8; padding:20px 0;">
    <tr>
        <td align="center">
            <table width="680" cellpadding="0" cellspacing="0"
                   style="background:#ffffff; border-radius:8px; overflow:hidden;">

               
                <tr>
                    <td style="
                        padding:30px 20px;
                        text-align:center;
                        background: red;
                    ">
                        <img src="https://dev.starlinkinfluencers.in/web/public/logo/star-login.jpg"
                             alt="FIMA Logo"
                             style="height:90px; width:auto; display:block; margin:0 auto;">

                        <div style="
                            font-size:22px;
                            font-weight:700;
                            color:#ffffff;
                            margin-top:12px;
                            letter-spacing:0.5px;
                        ">
                            StarLink
                        </div>
                    </td>
                </tr>

                {{-- CONTENT --}}
                <tr>
                    <td style="padding:24px; font-size:15px; color:#374151; line-height:1.6;">

                         <p>
							Hello Team,
						 </p>
						 <p>
							<b>{{ $user}} </b> has reported that below order had not been receive.
						 </p>

                       
                      <table width="100%" cellpadding="8" cellspacing="0" 
						   style="border-collapse: collapse; margin-top:20px; background:#fafafa;">

						<tr>
							<td style="border:1px solid #ddd; font-weight:bold; width:35%;">Order ID</td>
							<td style="border:1px solid #ddd;">{{ $order_id }}</td>
						</tr>

						<tr>
							<td style="border:1px solid #ddd; font-weight:bold;">Current Delivery Status</td>
							<td style="border:1px solid #ddd;"> Delivered </td>
						</tr>

						<tr>
							<td style="border:1px solid #ddd; font-weight:bold;">Order Date</td>
							<td style="border:1px solid #ddd;">{{ $order_date }}</td>
						</tr>

						<tr>
							<td style="border:1px solid #ddd; font-weight:bold;">Name</td>
							<td style="border:1px solid #ddd;">{{ $name }}</td>
						</tr>
						<tr>
							<td style="border:1px solid #ddd; font-weight:bold;">Phone No</td>
							<td style="border:1px solid #ddd;">{{ $phone }}</td>
						</tr>

					</table>
					<p>
					Please review the order & take the necessary action.
					</p>

                    </td>
                </tr>
				

                {{-- FOOTER --}}
                <tr>
                    <td style="
                        padding:16px 24px;
                        font-size:13px;
                        background: red;
                        text-align:center;
                        color:#ffffff;
                    ">
                        © StarLink. All rights reserved.
                        <br>
                        <small>If you believe you received this message in error, please contact us.</small>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
