/**
 * This module will load all models into memory automatically.
 * No exporting as there isn't really anything else to do here
 */

var sys = require('sys'),
    p = require('path'),
    fs = require('fs'),
    Step = require('step');

//loop through every file in the models directory and require it.
var dir = './models';


//let's try a different approach...
var filePath;

exports.init = [
    function checkpath() {
        fs.realpath(dir, this);
    },
    function readdir(err, path) {
        if (err) {
            sys.log(sys.inspect(err));
            throw err;
        }
        sys.log(sys.inspect(path));
        filePath = path;
        fs.readdir(path,this);
    },
    function loadfiles(err, files) {
        if (err) {
            sys.log(sys.inspect(err));
            throw err;
        }
        sys.log(sys.inspect(files));
        files.each(function(file){
            file = p.basename(file,'.js');
            require(filePath+'/'+file);
        });
        return null;
    }
];

/**
exports.init = function(next){
    var filePath;
    Step(
        function checkpath() {
            fs.realpath(dir, this);
        },
        function readdir(err, path) {
            if (err) {
                sys.log(sys.inspect(err));
                return;
            }
            sys.log(sys.inspect(path));
            filePath = path;
            fs.readdir(path,this);
        },
        function loadfiles(err, files) {
            if (err) {
                sys.log(sys.inspect(err));
                next();
            }
            sys.log(sys.inspect(files));
            files.each(function(file){
                file = p.basename(file,'.js');
                require(filePath+'/'+file);
            });
            return;
        },
        next

    );
};
 **/

