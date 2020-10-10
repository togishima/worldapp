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
      .selector-wrap {
        position: absolute;
        bottom: 1.5em;
        right: 2em;
        display: flex;
        flex-direction: column;
        color: #fff;
      }
      .selector-label {
        align-self: center;
      }
      .selector {
        border-radius: 4px;
        font-size: 1.5em;
        background: #707070
      }

      .country-info-wrap {
        position: absolute;
        bottom: 1.5em;
        left: 2em;
        padding: 0.5em 1em;
        color: #fff;
        background: rgba(112, 112, 112, 0.3);
      }

    </style>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
    <script>
      let data = @json($data);
    </script>
  </head>
  <body>
    <h1 class="title">Visualization of International Migration</h1>
    
    <div id="globalArea" class="globe"></div>
    
    <div class="country-info-wrap">
      <h2>Country Information</h2>
      {!! $countryInfoDOM !!}
    </div>

    <div class="selector-wrap">
      <label for="year-selector" class="selector-label">Change Data Year:</label>
      <select name="year_selector" id="year_selector" class="selector">
        @foreach ($data['dataSetKeys'] as $data)
        <option value="{{$data}}">{{$data}}</option>
        @endforeach
      </select>

      <label class="selector-label" for="country-selector">Change Country Data：</label>
      <select id="country_selector" class="selector" name="country_selector">
        @foreach($countryList as $country)
        <option name="{{$country->Name}}" value="{{$country->Code2}}" @if($country->Name == "Japan") selected @endif>{{$country->Name}}</option>
        @endforeach
      </select>
    </div>

    <script>
      
    </script>
  </body>
</html>
