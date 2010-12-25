/*
---

name: Jx.Module

description: Base class for all JxCMS admin modules

license: MIT-style license.

requires:
 - jxlib/Jx.Panel
 - jxlib/Jx.Splitter
 - jxlib/Jx.Splitter.Snap

css:

images:

provides: [Jx.Module]

...
 */

Jx.Module = new Class({

    Extends: Jx.Widget,

    options: {
        label: ''
    },

    addOnPanels: null,
    mainArea: null,
    addOns: null,
    addOnClosed: null,
    notifier: null,
    tab: null,

    init: function(){
        this.addOnPanels = [];
        this.addOnClosed = true;
        this.parent();
    },

    render: function(){

        this.parent();

        //create a tab
        this.tab = new Jx.Tab({active: true,
            close: true,
            label: this.options.label
        });

        //add a reference to the module so we can get it from the tab
        this.tab.module = this;
    },

    /**
     * Creates the interface for the module. Called after the tab is added to the
     * $content TabBox.
     */
    createInterface: function() {
        //split the area with a splitter so we can have an
        //add on split pane
        this.splitter = new Jx.Splitter(this.tab.content,{
            layout: 'horizontal',
            containerOptions: [{width: null},{width: 300}],
            barOptions: [{snap: 'after'}]
        });
        this.mainArea = this.splitter.elements[0];
        this.addOns = this.splitter.elements[1];

        //snap the bar closed to start....
        //this.splitter.bars[0].fireEvent('dblclick');
    },

    /**
     * Register a panel to be displayed in the add-on split
     * pane.
     */
    registerAddOnPanel: function(panel) {
        this.addOnPanels.include(panel);

        if (this.addOnClosed) {
            //open the addOn pane
            this.splitter.bars[0].fireEvent('dblClick');
        }
    },

    setNotifier: function (notifier) {
        this.notifier = notifier;
    }

});
