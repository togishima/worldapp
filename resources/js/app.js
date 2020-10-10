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
    "autoRotation": true,
    "rotationRatio": 0.7
  },
  "color": {
    "surface": 1749093,
    "selected": 1827421,
    "in": 2715903,
    "out": 14867488,
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
  updateCountryInfo(countryCode);
  controller.setAutoRotation(false);
};

$('[name=year_selector').on('change', function () {
  let year = $(this).val();
  controller.switchDataSet(year);
  console.log(`datasetChanged, current data set is ${year}`);
});

$('[name=country_selector]').on('change', function () {
  var countryCode = $(this).val();
  controller.switchCountry(countryCode);
  fetchData(countryCode);
  updateCountryInfo(countryCode);
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


function fetchData(countryCode) {
  $.ajax({
    url: `/json/${countryCode}`,
    type: 'GET',
    dataType: 'json'
  }).done(function (results) {
    console.log(results);
    controller.addData(results);
    //controller.init();
  }).fail(function (jqHXR, textStatus, errorThrown) {
    alert('ファイルの取得に失敗しました。');
    console.log("ajax通信に失敗しました")
    console.log(jqXHR.status);
    console.log(textStatus);
    console.log(errorThrown.message);
  });
};
