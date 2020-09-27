<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <style>
      html, body{
        position: relative;
        margin: 0;
      }
      .globe {
        height: 100vh;
        width: 100vw;
      }
      .title {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        margin: 0;
        padding: 0.3em 0;
        text-align: center;
        background: rgba(0, 0, 0, 0.6);
        color: White;
      }
      .country-selector-wrap {
        position: absolute;
        bottom: 1em;
        right: 0;
        margin-right: 2em;
        color: #fff;
      }
      .country-selector {
        border-radius: 4px;
        font-size: 1.5em;
        background: #707070
      }

    </style>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script>
      let data = @json($data); 
      console.log(data);
    </script>
  </head>
  <body>
    <h1 class="title">Visualization of International Migration</h1>
    <div id="globalArea" class="globe"></div>
    <div class="country-selector-wrap">
      <label for="country-selector">Change Country Data：</label>
      <select id="country_selector" class="country-selector" name="country_selector">
      @foreach($country_list as $country)
      <option name="{{$country->Name}}" value="{{$country->Code2}}" @if($country->Name == "Japan") selected @endif>{{$country->Name}}</option>
      @endforeach
    </select>
    </div>
  </body>
</html>
