
var mongoose = require('mongoose/mongoose').Mongoose;
    b64 = require('utils/lib/base64');

mongoose.model('Session', {

    properties: ['data', 'updated_at'],

    getters: {
        data: function(){
            //base64 decode session data
            return b64.decode(this.data);
        }
    },

    methods: {
        save: function(fn){
            this.updated_at = new Date();
            this.data = b64.encode(this.data);//base64 encode
            this.__super__(fn);
        }
    }

});