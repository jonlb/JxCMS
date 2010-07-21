
var router = require('router').Router,
    sys = require('sys');

//let's test the events..
core.addEvent('routeAdded', function(){sys.log('caught routeAdded event in module');});

//test mongodb...
