/*
---

name: modules

description: Admin component for managing modules

license: MIT-style license.

requires:
 - jxlib/Jx.Store.Parser.JSON
 - jxlib/Jx.Store.Protocol.Ajax
 - jxlib/Jx.Store.Strategy.Full
 - jxlib/Jx.Store.Strategy.Save
 - jxlib/Jx.Store.Strategy.Sort
 - jxlib/Jx.Store
 - jxlib/Jx.Field.File
 - jxlib/Jx.Notice
 - jxlib/Jx.Notifier
 - jxlib/Jx.Button
 - jxlib/Jx.ListView
 - jxlib/Jx.ListItem
 - jxlib/Jx.Splitter
 - jxlib/Jx.Panel.FileUpload
 - Jx.Module
 - jxlib-extensions/Jx.Adaptor.ListView.Fill

css:
 - modules

images:
 - brick.png
 - brick_add.png
 - brick_delete.png
 - brick_edit.png
 - brick_error.png
 - brick_go.png
 - brick_link.png

provides: [modules]

...
 */

Jx.Modules = new Class({

    Extends: Jx.Module,

    name: 'modules',
    
    createInterface: function () {

        this.parent();

        //use a layout to divide the top/bottom
        var splitV = new Jx.Splitter(this.splitter.elements[0],{
            layout: 'vertical',
            useChildren: false,
            containerOptions: [{height: 100},{height: null}]
        });
/*
        //split the bottom using a splitter
        var splitH = new Jx.Splitter(splitV.elements[1],{
            layout: 'horizontal'
        });
*/
        //add the upload panel to the top section
        var uploadPanel  = new Jx.Panel.FileUpload({
            parent: splitV.elements[0],
            label: 'Upload Module File(s)',
            file: {
                progress: false,
                handlerUrl: '/admin/modules/upload',
                id: 'file-upload-test',
                name: 'file-upload-test',
                label: 'File to Upload',
                debug: true,
                mode: 'single',
                autoUpload: true
            },
            prompt: '',
            removeOnComplete: true,
            collapse: false,
            onFileUploadComplete: function(filename){
                log("File " + filename + " Uploaded.");
            },
            onAllUploadsCompleted: function() {
                log("All Files Uploaded.");
            }
        });


        this.installedView = new Jx.ListView();

        //add panels with listviews to the two sides
        var installed = new Jx.Panel({
            parent: splitV.elements[1],
            label: 'Currently Installed Modules',
            collapse: false,
            content: this.installedView
        });

        /*
        this.waitingView = new Jx.ListView();
        var toInstall = new Jx.Panel({
            parent: splitH.elements[1],
            label: 'Modules ready to be installed',
            collapse: false,
            content: this.waitingView
        });
*/
        //this.content.resize();

        var parser = new Jx.Store.Parser.JSON({secure: true});
        var protocol = new Jx.Store.Protocol.Ajax({
            parser: parser,
            urls: {
                read: '/admin/modules/all.json',
                update: '/admin/modules/update.json'
            }
        });
        var full = new Jx.Store.Strategy.Full();
        var save = new Jx.Store.Strategy.Save({autoSave: true});
        var sort = new Jx.Store.Strategy.Sort({
            sortCols: ['permanent','active','name']
        });
        this.installedStore = new Jx.Store({
            strategies: [full, save, sort],
            protocol: protocol,
            record: Jx.Record,
            columns: [{
                name: 'id',
                type: 'numeric'
            },{
                name: 'active',
                type: 'boolean'
            },{
                name: 'name',
                type: 'alphanumeric'
            },{
                name: 'version',
                type: 'alphanumeric'
            },{
                name: 'permanent',
                type: 'boolean'
            }],
            recordOptions: {
                primaryKey: 'id'
            }
        });
        //add listView.Fill adaptor
        var adaptor = new Jx.Adaptor.ListView.Fill({
            itemTemplate: "<li class='jxListItemContainer jxcmsModuleItem'><a class='jxListItem' href='javascript:void(0);'><img src='"+Jx.aPixel.src+"' class='itemImg jxcmsModuleIcon'><span class='itemLabel'>{label}</span><span class='itemTools'><img src='"+Jx.aPixel.src+"' class='itemImg jxcmsActivateButton'/><img src='"+Jx.aPixel.src+"' class='itemImg jxcmsModulesUninstall' title='Uninstall Module'/></a></li>",
            template: '{name} : Version {version}',
            store: this.installedStore,
            onItemCreated: this.alterInstalledListItem.bind(this)
        });
        adaptor.attach(this.installedView);
        //this.store.addEvent('storeDataLoaded',this.populateInstalled.bind(this));


        this.installedStore.load();
        //this.loadWaiting();

    },

    alterInstalledListItem: function(item, record) {
        item = document.id(item);
        //add active class/title
        var activateLink = item.getElement('.jxcmsActivateButton');
        //add permanent class
        if (record.get('permanent')){
            item.addClass('jxcmsModulePermanent');
            activateLink.dispose();
            item.getElement('.jxcmsModulesUninstall').dispose();
            item.getElement('.jxcmsModuleIcon').addClass('active');
        } else {
            if (record.get('active')) {
                activateLink.addClass('deactivate').set('title','Deactivate Module' ).addEvent('click', this.update.bind(this, [item, false]));
                item.getElement('.jxcmsModuleIcon').addClass('active');
            } else {
                activateLink.addClass('activate').set('title','Activate Module' ).addEvent('click', this.update.bind(this, [item, true]));
                item.getElement('.jxcmsModuleIcon').addClass('inactive');
            }
        }
    },

    loadWaiting: function () {
        $request.callServer(null,{
            url: '/admin/modules/waiting.json',
            events: {
                onSuccess: this.populateWaiting.bind(this)
            }
        });
    },

    populateWaiting: function (data) {
        if (data.modules.length === 0) {
            //no modules waiting, display message
            var templ = "<li class='jxListItemContainer jxcmsModuleItem'><a class='jxListItem' href='javascript:void(0);'><span class='itemLabel'>No modules are waiting to be installed.</span></a></li>";
            var item = new Jx.ListItem({template:templ, enabled: true});
            this.waitingView.add(item);
        } else {
            //populate listView
            this.waitingView.list.empty();
            var templ = "<li class='jxListItemContainer jxcmsModuleItem'><a class='jxListItem' href='javascript:void(0);'><img src='"+Jx.aPixel.src+"' class='itemImg inactive'><span class='itemLabel'>{name}</span><span class='itemTools'><img src='"+Jx.aPixel.src+"' class='itemImg jxcmsModulesRemove' title='Remove Waiting Module'/><img src='"+Jx.aPixel.src+"' class='itemImg jxcmsModulesInstall' title='Install Module'/></a></li>";
            this.waiting = data.modules;
            data.modules.each(function(module){
                var o = {};
                o.name = module.name;
                var theTemplate = new String(templ).substitute(o);
                var item = new Jx.ListItem({template:theTemplate, enabled: true});
                $(item).store('moduleName', o.name);
                this.waitingView.add(item);
                $(item).getElement('.sgdModulesInstall').addEvent('click', this.install.bind(this, item));
                $(item).getElement('.sgdModulesRemove').addEvent('click', this.remove.bind(this, item));
            },this);
        }
    },

    update: function (item, activate) {

        //change this to use the store to alter the activated state
        item = document.id(item);
        var id = item.retrieve('moduleId');
        var n = this.store.findByColumn('id',id);
        this.store.set('active',activate,n);
        var class = activate ? '.deactivate' : '.activate';
        var newClass = !activate ? '.deactivate' : '.activate';
        item.getElement(class).removeClass(class).addClass(newClass);

    },

    uninstall: function (item) {
        var name = $(item).retrieve('moduleName');

        var obj = { name: name };
        $request.callServer(obj,{
            url: '/admin/modules/uninstall.json',
            events: {
                onSuccess: this.afterUninstall.bind(this)
            }
        });
    },

    uploadError: function (data) {
        if ($defined(data.error)) {
            this.notifier.add(new Jx.Notice.Error({content: data.error.message}));
        }
    },

    install: function (item) {
        var name = $(item).retrieve('moduleName');

        var obj = { name: name };
        $request.callServer(obj,{
            url: '/admin/modules/install.json',
            events: {
                onSuccess: this.afterInstall.bind(this, item)
            }
        });
    },

    afterInstall: function (item) {
        this.waitingView.remove(item);
        this.store.load();
    },

    afterUninstall: function () {
        this.store.load();
    },

    afterUpdate: function () {
        this.store.load();
    },

    remove: function (item) {
        var name = $(item).retrieve('moduleName');

        this.waitingView.remove(item);

        var obj = { name: name };
        $request.callServer(obj,{
            url: '/admin/modules/remove.json'
        });
    }

});

$moduleManager.register(new Jx.Modules({
    label: 'Manage Modules',
    id: 'Modules'
}));
