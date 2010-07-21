var sys = require('sys'),
    request = require('request').Request,
    response = require('response').Response;

(function(){

var Route = new Class({

    method: null,
    re: null,
    callback: null,
    name: null,

    methods: ['GET','POST','PUT','DELETE'],

    initialize: function(name, method, re, callback) {
        sys.log('in Route initialize...\n');
        if (this.methods.contains(method) || method == '*') {
            this.method = method;
        } else {
            this.method = '*';
        }

        this.name = name;
        this.re = re;
        this.callback = callback;
    },

    match: function(uri) {
        //test if we have a matching uri.
        sys.log('uri:'+sys.inspect(uri));
        sys.log('re: '+sys.inspect(this.re));
        if (this.re.test(uri)) {
            //get the captures
            var parts = uri.match(this.re);
            return {
                fn: this.callback,
                params: parts
            }
        }
        return false;

    }

});


var Router = {

    /**
     *
     * @param urls an array of arrays in which the inner arrays are of the form
     *          ["route_name","route_regex",callback]
     */
    add: function(urls) {

        var url_re = /^(\w+|\*)(\s(.*))?$/;
        urls.each(function(url){
            var parts = url[1].match(url_re);
            sys.log('parts: '+sys.inspect(parts));
            if (nil(parts)) {
                sys.log('returning false');
                return false;
            }
            sys.log('adding route...');
            Router.routes.push(new Route(url[0],parts[1],new RegExp('^'+(parts[3] || '')),url[2]));
            core.fireEvent('routeAdded');
        },this);


        return true;
    },

    get: function(name) {
        var r;
        Router.routes.each(function(route){
            if (route.name == name) {
                r = route;
            }
        },this);
        return r;

    },

    all: function() {
        return Router.routes;
    },

    dispatch: function(req, resp) {
        //wrap the request and response
        req = new request(req);
        resp = new response(resp);

        Router.routes.each(function(route){
            ret = route.match(req.uri);
            sys.log(sys.inspect(ret));
            if (ret !== false) {
                ret.fn.run(req, resp, ret.params);
            }
        },this);

    }

};

Router.routes = [];

exports.Router = Router;


})();