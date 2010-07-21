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
require.paths.unshift('./vendor');
require.paths.unshift('./system');
require.paths.unshift('./config');

//require mootools so we can use Class everywhere
require('mootools').apply(GLOBAL);
var sys = require('sys');

//pull in the global config
var config = require('global').global;

//setup the global core object
GLOBAL.core = new (require('core').core)(config);
require('modules');

//test the route parsing
var router = require("router").Router;

router.add([['test','GET /admin/(\\w+)/(\\d+)$', function(){
    sys.log('successfully routed!!!');
}]]);

sys.log(sys.inspect(router.get('test')));

//now try to dispatch...
router.dispatch({uri:'/admin/users/2'},{});








