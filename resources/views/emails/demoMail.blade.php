<!DOCTYPE html>
<html>
<head>
    
</head>
<body>
   <b>Mason Name : </b>{{$mailData['mason_name'] ?? ""}}<br>
   <b>Mason Email : </b>{{$mailData['mason_email'] ?? ""}}<br>
   <b>Mason Phone : </b>{{$mailData['mason_phone'] ?? ""}}<br>
    <p>
        <b>Message : </b> <br>
        {{ $mailData['msg'] }}
    </p>
  
    
</body>
</html>