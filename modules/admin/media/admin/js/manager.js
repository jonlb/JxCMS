/*
---

name: Jx.Manager.Module

description: Module for managing the addition/activation/removal of admin modules in JxCMS

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

    getAllPlugins: function () {
        //ajax call to get list of plugins
        if (this.request) {
            this.request.callServer(null,{
                url: this.options.url,
                method: 'get',
                events: {
                    onSuccess: this.processPlugins.bind(this)
                }
            });
        }
    },

    processPlugins: function(data) {
        //process module list
        console.log(data);
        if ($defined(data.modules)) {
            $H(data.modules).each(function(value, key){
                if ($defined(value.files)) {
                    $uses(value.files, null, null, function(){});
                }
            },this);
        }
    }
    
});