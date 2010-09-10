/*
---

name: request.mod

description:

license: MIT-style license.

requires:
 - more/Spinner
 - core/request



provides: [request.mod]

...
 */


Request = Class.refactor(Request, {
    initialize: function(options){
		this.xhr = new Browser.Request();
		this.setOptions(options);
		this.options.isSuccess = this.options.isSuccess || this.isSuccess;
		this.headers = new Hash(this.options.headers);
	}
});
