/*
---

name: MenuSystem

description:

license: MIT-style license.

requires:
 - jxlib-extensions/Jx.Request
 - jxlib/Jx.ButtonSet
 - jxlib/Jx.Toolbar
 - jxlib/Jx.Menu
 - jxlib/Jx.Menu.Submenu
 - jxlib/Jx.Menu.Item

provides: [menuSystem]

...
 */
var MenuSystem = new Class({

    Implements: [Events, Options],

    options: {},

    initialize: function (menuOpts, options) {
        this.setOptions(options);

        //create the menu system
        this.menuOpts = menuOpts;

        //use a buttonset so only one item is checked at a time
        //this.set = new Jx.ButtonSet();

        //create the menubar
        this.menuContainer = $('menu-container');
        this.menubar = new Jx.Toolbar({parent: this.menuContainer, scroll: false});

        this.topLevelMenus = $H();
        //var modules = new Jx.Menu({label: 'System'});

        //loop through the menuOpts and if it's a toplevel menu make it a main menu
        //then grab any existing submenu and create that recursively.
        //if the menu doesn't have an toplevel attribute then add it to the modules menu
        $H(menuOpts).each(function(menu, key){
            var tmenu = this.createMenu(menu);
            this.topLevelMenus.set(key,tmenu);
        },this);

        //this.topLevelMenus.set('modules',modules);
        //add all topLevel menus to the toolbar
        this.topLevelMenus.each(function(menu){
            this.menubar.add(menu);
        },this);
    },

    createMenu: function (opts) {
        var menu;
        if ($defined(opts.toplevel)) {
            //create new top main menu
            menu = new Jx.Menu({
                label: opts.text,
                tooltip: opts.title
            });

            if ($defined(opts.submenu)) {
                $H(opts.submenu).each(function(m,key){
                    var tmenu = this.createMenu(m);
                    menu.add(tmenu);
                },this);
            }
        } else if ($defined(opts.submenu)) {
            //create a submenu
            menu = new Jx.Menu.SubMenu({
                label: opts.text,
                //toggle: true,
                tooltip: opts.title
            });
            if (opts.selected) {
                menu.setActive(true);
            }
            $H(opts.submenu).each(function(m){
                var tmenu = this.createMenu(m);
                menu.add(tmenu);
            },this);
        } else {
            //it's just a menu item
            menu = new Jx.Menu.Item({
                label: opts.text,
                tooltip: opts.title,
                //toggle: true,
                onClick: function () {
                    this.loadModule(opts);
                }.bind(this)

            });
            if (opts.selected) {
                menu.setActive(true);
            }
        }
        return menu;
    },

    loadModule: function (opts) {
        //check for whether this is already loaded...
        //first get all tabs
        //console.log($content);

        var found = false;
        tab = $moduleManager.findByLabel(opts.text);
        if (tab) {
            if (tab.isActive()) {
                document.id($content.tabbar).getParent().retrieve('jxBarContainer').scrollIntoView(tab);
                found = true;
            } else {
                tab.setActive(true);
                found = true;
            }
        }
        if (!found) {
            $content.setBusy(true);
            $uses(opts.file,null,null,function(){$content.setBusy(false);});
        } else {
            $moduleManager.fireEvent('itemAdded', [tab]);
        }
    },

    processReturn: function (responseTree, responseElements, responseHTML, responseJavaScript) {
       $exec(responseJavaScript);
    }

});
