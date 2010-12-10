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

    Extends: Jx.Panel,

    options: {
        hideTitle: true,
        notifier: null,
        close: false,
        maximize: false,
        collapse: false
    },

    addOnPanels: null,

    mainArea: null,
    addOns: null,

    addOnClosed: null,

    init: function(){
        this.addOnPanels = [];
        this.addOnClosed = true;
        this.parent();
    },

    render: function(){
        this.parent();

        //split the area with a splitter so we can have an
        //add on split pane
        this.splitter = new Jx.Splitter(this.content,{
            layout: 'horizontal',
            useChildren: false,
            containerOptions: [{resizeWithWindow: true},{resizeWithWindow: true, width: 300}],
            barOptions: [{snap: 'after'}]
        });

        this.mainArea = splitter.elements[0];
        //eventually this should be a panelset or similar accordian that holds panels.
        this.addOns = splitter.elements[1];

        //snap the bar closed to start....
        this.splitter.bars[0].fireEvent('dblclick');
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
    }

});
