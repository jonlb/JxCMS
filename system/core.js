/**
 * Core of the new system.
 *
 * Will use this as a way of creating an event system...
 */



var core = new Class({

    Implements: [Events, Options],

    version: '0.1.1dev',

    options: {
        mode: 'development'
    },

    init: false,

    initialize: function(options){
        this.setOptions(options);
        

    }
});


exports.core = core;