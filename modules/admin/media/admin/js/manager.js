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
        type: 'manager'
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
    }
    
});