window.three = require('three');
window.GIO = require('giojs');

const container = document.getElementById("globalArea");
const configs = {
  "control": {
    "stats": false,
    "disableUnmentioned": false,
    "lightenMentioned": false,
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
  },
  "brightness": {
    "ocean": 0,
    "mentioned": 0,
    "related": 0.5
  }
}
const controller = new GIO.Controller(container, configs);
controller.setInitCountry("JP");
controller.addData(data);
controller.init();

// get scene after controller initialized
var scene = controller.getScene();

// create a universe background which is an Three.js object
var universe = createUniverse();

// add universe to the scene
scene.add(universe);

$('[name=country_selector]').on('change', function () {
  var countryCode = $(this).val;
  console.log(countryCode);
});