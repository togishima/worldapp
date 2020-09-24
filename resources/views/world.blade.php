<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <title>Laravel</title>
    <script>
      const data = @json($data);
      console.log(data);
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
  </head>
  <body>
    <div id="globalArea" style="width:100vw;height:100vh"></div>
  </body>
</html>
