/*
---

name: Security

description: Admin plugin for providing Security panels in the JxCMS admin interface

license: MIT-style license.

requires:
 - jxlib/Jx.Object

provides: [Security]

...
 */

var Security = new Class({

    Extends: Jx.Object,

    options: {},

    init: function() {
        //register with module manager to get notified of new modules added
        $moduleManager.addEvent('itemAdded',this.addedModule.bind(this));
    },

    addedModule: function(tab){
        if ($defined(tab.module)) {
            var module = tab.module;
            var panel = this.getPanelFor(module.name);
            if ($defined(panel)) {
                module.registerAddOnPanel(panel);
            }
        }
    },

    getPanelFor: function(name){
        
    }
});


var sec = new Security();