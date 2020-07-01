<html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="keywords" content="your keywords" />
    <meta name="description" content="your description" />
    <style>
          img{
              border-radius: 50px;
              width: 41px;
              height: 41px;
              margin-left:-5px;
              margin-top:-5px;
          }
    </style>
</head>
<body>
<img src="{{$agentInfo =="" ? "" : $agentInfo->ImageUrl}}" alt="" />
</body>


</html>
