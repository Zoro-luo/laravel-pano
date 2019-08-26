<html>
    <head>
        <title>PANO-{{$id}}-</title>
        <meta charset="utf-8"/>
        <style>
               body,iframe{
                   margin: auto;
                   border: 0px;
               }
        </style>
    </head>
    <body>
          <iframe width="100%" height="100%" src="{{url('/pano/'.$id)}}"></iframe>
    </body>
</html>