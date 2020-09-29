require('./bootstrap');

//gio.js
window.three = require('three');
window.GIO = require('giojs');

const container = document.getElementById("globalArea");
const configs = {
  "control": {
    "stats": false,
    "disableUnmentioned": true,
    "lightenMentioned": true,
    "inOnly": false,
    "outOnly": false,
    "initCountry": "CN",
    "halo": true,
    "transparentBackground": true,
    "autoRotation": false,
    "rotationRatio": 1
  },
  "color": {
    "surface": 1749093,
    "selected": 1827421,
    "in": 5111795,
    "out": 14831145,
    "halo": 9895823,
    "background": 0
  },
  "brightness": {
    "ocean": 0,
    "mentioned": 0.5,
    "related": 0.5
  }
}
const controller = new GIO.Controller(container, configs);

controller.setInitCountry("JP");
controller.addData(data);
controller.init();

controller.onCountryPicked(callback);
function callback(selectedCountry) {
  let countryCode = selectedCountry.ISOCode;
  $.ajax({
    url: `/data/${countryCode}`,
    type: 'GET',
    dataType: 'json'
  }).done(function (results) {
    controller.switchCountry(countryCode);
    newInfo = results[0];
    console.log(newInfo);
    document.getElementById("c-name").innerHTML = newInfo.Country_Name;
    document.getElementById("govt-form").innerHTML = newInfo.GovernmentForm;
    document.getElementById("c-pop").innerHTML = (newInfo.Population / 1000000) + "M";
    document.getElementById("c-gnp").innerHTML = newInfo.GNP;
    document.getElementById("c-cap").innerHTML = newInfo.Capital;

  }).fail(function (jqHXR, textStatus, errorThrown) {
    alert('ファイルの取得に失敗しました。');
    console.log("ajax通信に失敗しました")
    console.log(jqXHR.status);
    console.log(textStatus);
    console.log(errorThrown.message);
  });
};

$('[name=year_selector').on('change', function () {
  let year = $(this).val();
  controller.switchDataSet(year);
  console.log(`datasetChanged, current data set is ${year}`);
});

$('[name=country_selector]').on('change', function () {
  var countryCode = $(this).val();
  $.ajax({
    url: `/json/${countryCode}`,
    type: 'GET',
    dataType: 'json'
  }).done(function (results) {
    controller.clearData();
    controller.addData(results);
    controller.switchCountry(countryCode);
    $('[name=year_selector').val(2013);
    updateCountryInfo(countryCode);
    //controller.init();
  }).fail(function (jqHXR, textStatus, errorThrown) {
    alert('ファイルの取得に失敗しました。');
    console.log("ajax通信に失敗しました")
    console.log(jqXHR.status);
    console.log(textStatus);
    console.log(errorThrown.message);
  });
});

function updateCountryInfo(countryCode) {
  $.ajax({
    url: `/data/${countryCode}`,
    type: 'GET',
    dataType: 'json'
  }).done(function (results) {
    newInfo = results[0];
    console.log(newInfo);
    document.getElementById("c-name").innerHTML = newInfo.Country_Name;
    document.getElementById("govt-form").innerHTML = newInfo.GovernmentForm;
    document.getElementById("c-pop").innerHTML = (newInfo.Population / 1000000) + "M";
    document.getElementById("c-gnp").innerHTML = newInfo.GNP;
    document.getElementById("c-cap").innerHTML = newInfo.Capital;
  });
};

/*
$('[name=country_selector]').on('change', function () {
  var countryCode = $(this).val;

  $.ajax({
    url: `/json/${countryCode}`,
    type: 'GET',
    dataType: 'json'
  }).done(function (results) {
    controller.clearData();
    controller.addData(results);
    //controller.init();
  }).fail(function (jqHXR, textStatus, errorThrown) {
    alert('ファイルの取得に失敗しました。');
    console.log("ajax通信に失敗しました")
    console.log(jqXHR.status);
    console.log(textStatus);
    console.log(errorThrown.message);
  });
});
*/
