/**
 * This is the main file for the cms. Run it by typing
 *
 * node cms.js
 *
 * at a command line.
 *
 * 
 */

//add current system paths to require
require.paths.unshift('./vendor', './system', './config', './models');

//require mootools so we can use Class everywhere
require('mootools').apply(GLOBAL);
var sys = require('sys'),
    model = require('models'),
    config = require('global').global,  //global config
    Step = require('step');


//setup the global core object
GLOBAL.core = new (require('core').core)(config);

//load in all models defined in ./models/ directory

var steps = Array.from(model.init);
steps.push(function done(){
      sys.log('=> DONE');
  }
);

sys.log(sys.inspect(steps));

Step.run(steps);




/**


//test the route parsing
var router = require("router").Router;

router.add([['test','GET /admin/(\\w+)/(\\d+)$', function(){
    sys.log('successfully routed!!!');
}]]);

sys.log(sys.inspect(router.get('test')));

//now try to dispatch...
router.dispatch({uri:'/admin/users/2'},{});


**/





