/*
---

name: modules

description: Admin component for managing modules

license: MIT-style license.

requires:
 - jx-extensions/Jx.Section
 - jxlib/Jx.Store.Parser.JSON
 - jxlib/Jx.Store.Protocol.Ajax
 - jxlib/Jx.Store.Strategy.Full
 - jxlib/Jx.Store.Strategy.Save
 - jxlib/Jx.Store
 - jxlib/Jx.Panel
 - jxlib/Jx.Field.File
 - jxlib/Jx.Notice
 - jxlib/Jx.Notifier
 - jxlib/Jx.Button
 - jxlib/Jx.ListView
 - jxlib/Jx.ListItem

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

    Extends: Jx.Panel,

    options: {
        hideTitle: true,
        notifier: null,
        close: false,
        maximize: false,
        collapse: false
    },
    
    render: function () {

        this.domObjA = new Element('div', {
        	'class': 'jxcmsPanelBody'
        });
        //file upload section
        var uploadBody = new Element('div');
        this.uploader = new Jx.Field.File({
            id: 'moduleUpload',
            name: 'moduleUpload',
            label: 'Choose a file to upload',
            progress: true,
            progressIDUrl: '/admin/modules/uploadProgressID.json',
            handlerUrl: '/admin/modules/upload',
            progressUrl: '/admin/modules/progress.json',
            onUploadComplete: function(data){
                this.notifier.remove(this.notice);
                this.loadWaiting();
            }.bind(this),
            onUploadBegin: function (){
                this.notice = new Jx.Notice({
                    content: 'Beginning File Upload...'
                });
                this.notifier.add(this.notice);
            }.bind(this),
            onUploadError: this.uploadError.bind(this),
            onFileSelected: function () {
                this.uploadButton.setEnabled(true);
            }.bind(this)
        });
        this.uploader.addTo(uploadBody);
        //upload button
        this.uploadButton = new Jx.Button({
            label: 'Upload Module File',
            enabled: false,
            onClick: function () {
                this.uploader.upload();
            }.bind(this)
        });
        this.uploadButton.addTo(uploadBody);
        if (!$defined(this.options.notifier)) {
            this.notifier = new Jx.Notifier({
                parent: uploadBody
              });
        } else {
            this.notifier = this.options.notifier;
        }

        new Jx.Section({
            parent: this.domObjA,
            heading: 'Upload New/Updated Modules',
            body: uploadBody
        });

        //Installed modules
        this.installedBody = new Element('div');

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
        this.store = new Jx.Store({
            strategies: [full, save],
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
                name: 'permanent',
                type: 'boolean'
            },{
                name: 'version',
                type: 'alphanumeric'
            },{
                name: 'info',
                type: 'alphanumeric'
            }],
            recordOptions: {
                primaryKey: 'id'
            }
        });
        this.store.addEvent('storeDataLoaded',this.populateInstalled.bind(this));

        //TODO: use a ListView here
        this.installedView = new Jx.ListView();
        this.installedView.addTo(this.installedBody);

        new Jx.Section({
            parent: this.domObjA,
            heading: 'Currently Installed Modules',
            body: this.installedBody
        });

        //Modules waiting for install
        this.waitingBody = new Element('div');
        this.waitingView = new Jx.ListView();
        this.waitingView.addTo(this.waitingBody);

        new Jx.Section({
            parent: this.domObjA,
            heading: 'Modules Waiting to be Installed',
            body: this.waitingBody
        });

        this.options.content = this.domObjA;

        this.parent();
        this.store.load();
        this.loadWaiting();
    },

    populateInstalled: function () {
        //loop through store and add to the listView

        //empty the listview
        this.installedView.list.empty();
        var templ = "<li class='jxListItemContainer jxcmsModuleItem'><a class='jxListItem' href='javascript:void(0);'><img src='"+Jx.aPixel.src+"' class='itemImg {class}'><span class='itemLabel'>{name}</span><span class='itemTools'><img src='"+Jx.aPixel.src+"' class='itemImg {activateClass}' title='{activateTitle}'/><img src='"+Jx.aPixel.src+"' class='itemImg jxcmsModulesUninstall' title='Uninstall Module'/></a></li>";
        this.store.first();
        var flag = true;

        while (flag) {
            var o = {};
            o.name = this.store.get('name');
            var active = this.store.get('active');
            o['class'] = active?'active': 'inactive';
            o.activateClass = active?'deactivate': 'activate';
            o.activateTitle = active?'Deactivate Module': 'Activate Module';
            var theTemplate = new String(templ).substitute(o);
            var item = new Jx.ListItem({template:theTemplate, enabled: true});
            $(item).store('moduleId', this.store.get('id'));
            $(item).store('moduleName', this.store.get('name'));
            if (this.store.get('permanent')) {
                if (!active) {
                    $(item).getElement('.activate').dispose();
                } else {
                    $(item).getElement('.deactivate').dispose();
                }
                $(item).getElement('.jxcmsModulesUninstall').dispose();
            } else {
                if (!active) {
                    $(item).getElement('.activate').addEvent('click', this.update.bind(this, [item, true]));
                } else {
                    $(item).getElement('.deactivate').addEvent('click', this.update.bind(this, [item, false]));
                }
                $(item).getElement('.jxcmsModulesUninstall').addEvent('click', this.uninstall.bind(this, item));
            }
            this.installedView.add(item);

            if (this.store.hasNext()) {
                this.store.next();
            } else {
                flag = false;
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
        var name = $(item).retrieve('moduleName');

        var obj = {
            module: name,
            install: activate
        };
        $request.callServer(obj,{
            url: '/admin/modules/update.json',
            events: {
                onSuccess: this.afterUpdate.bind(this)
            }
        });
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

var modulePanel = new Jx.Modules({
    notifier: $noticeArea
});
var moduleTab = new Jx.Tab({
    active: true,
    close: true,
    label: 'Manage Modules',
    content: modulePanel
});
//let's just add the tab for now
$content.add(moduleTab);
