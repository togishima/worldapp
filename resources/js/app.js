require('./bootstrap');

//gio.js
window.three = require('three');
window.GIO = require('giojs');
var container = document.getElementById("globalArea");
var controller = new GIO.Controller(container);

controller.setInitCountry("JP");

controller.addData(data);
controller.init();
