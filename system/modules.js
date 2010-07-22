
var router = require('router').Router,
    sys = require('sys');

//let's test the events..
core.addEvent('routeAdded', function(){sys.log('caught routeAdded event in module');});

//test mongodb...
var mongoose = require('mongoose/mongoose').Mongoose;
require('modules.model');

 db = mongoose.connect('mongodb://localhost/test');

 User = db.model('User');

User.find({}).all(function(array){
    sys.log(sys.inspect(array));
});