/*
---

name: Jx.Manager.Module

description:

license: MIT-style license.

requires:
 - jxlib-extensions/Jx.Manager

css:
 - admin

provides: [Jx.Manager.Module]

...
 */


Jx.Manager.Module = new Class({

    Extends: Jx.Manager,

    options: {
        type: 'manager',
        request: null,
        url: null
    },

    request: null,

    init: function () {
        this.parent();
        if ($defined(this.options.request)) {
            this.request = this.options.request;
        }
    },

    findByLabel: function(label) {
        var mod = false;
        this.list.each(function(value, key){
            var l = value.getLabel();
            if ($defined(l) && l == label) {
                mod = value;
            }
        }, this);

        return mod;
    },

    getAllPermModules: function () {
        //ajax call to get list of permanent modules
        if (this.request) {
            this.request.callServer(null,{
                url: this.options.url,
                method: 'get'
            });
        }
    },

    processPermModules: function(data) {
        //process module list
    }
    
});