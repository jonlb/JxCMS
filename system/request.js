/**
 * Created by IntelliJ IDEA.
 * User: jon
 * Date: Jul 18, 2010
 * Time: 9:39:06 PM
 * To change this template use File | Settings | File Templates.
 */

var sys = require('sys');
//test if we can access core here


exports.Request = new Class({


    uri: null,
    method: null,
    params: null,

    /**
     *
     * @param req the ServerRequest object
     */
    initialize: function(req){
        this.uri = req.uri;
    },

    getUri: function(){
        return this.uri;
    },

    getMethod: function(){
        return this.method;
    },

    getParam: function(key, def) {
        def = $defined(def)? def : null;
        if ($defined(this.params[key])) {
            return this.params[key];
        } else {
            return def;
        }
    }
});