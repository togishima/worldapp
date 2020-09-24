require('./bootstrap');

//gio.js
window.three = require('three');
window.GIO = require('giojs');

var container = document.getElementById("globalArea");
var configs = {
  "control": {
    "stats": false,
    "disableUnmentioned": false,
    "lightenMentioned": false,
    "inOnly": false,
    "outOnly": false,
    "initCountry": "CN",
    "halo": true,
    "transparentBackground": false,
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
    "mentioned": 0,
    "related": 0.5
  }
}
var controller = new GIO.Controller(container, configs);

controller.setInitCountry("JP");
controller.addData(data);
controller.init();

