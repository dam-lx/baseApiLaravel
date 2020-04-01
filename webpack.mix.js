let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
const fs   = require('fs');
const path = require('path');
const jsEx   = ".js";
const sassEx = ".scss";

function trim (s,c) {
    s = s.replace(new RegExp(c,"g"), "").replace(new RegExp("\\\\", "g"), "/");
    return s;
}
function trimSass (s,c) {
    s = s.replace(new RegExp(c,"g"), "").replace('sass','css').replace('.scss','.css').replace(new RegExp("\\\\", "g"), "/");
    return s;
}

function walkSync (dir,extention, filelist = []) {
    fs.readdirSync(dir).forEach(file => {
        const dirFile = path.join(dir, file);
        try {
            filelist = walkSync(dirFile,extention, filelist);
        }
        catch (err) {
            if (err.code === 'ENOTDIR' || err.code === 'EBUSY'){
                if (dirFile.indexOf(extention) !== -1) {
                    filelist = [...filelist, dirFile];
                }
            }
            else throw err;
        }
    });
    return filelist;
}

mix.setPublicPath("public")
    .setResourceRoot('../../../');

// mix.browserSync('http://localhost:98/');
//===========================JS=============================================
{

    //=======================Mix Backend JS================================
    var filesBackendJS = walkSync("resources/js/backend", jsEx);
    filesBackendJS.forEach(function (filepath) {
        mix.js(filepath, 'public/' + trim(filepath, "resources"));
    });
    //=======================Mix Backend JS================================

    //=======================Mix Dev JS================================
    var filesDevdJS = walkSync("resources/js/dev", jsEx);
    filesDevdJS.forEach(function (filepath) {
        mix.js(filepath, 'public/' + trim(filepath, "resources"));
    });
    //=======================Mix Dev JS================================

    //=======================Mix Auth JS================================
    var filesAuthJS = walkSync("resources/js/auth", jsEx);
    filesAuthJS.forEach(function (filepath) {
        mix.js(filepath, 'public/' + trim(filepath, "resources"));
    });
    //=======================Mix Auth JS================================

    // //=======================Mix Lib JS================================
    mix.scripts('resources/js/lib/common.js','public/js/lib/common.js');
    mix.scripts('resources/js/lib/pace.min.js','public/js/lib/pace.min.js');
}
//===========================JS=========================================



//===========================SASS=============================================
{
    //=======================Mix Backend SASS================================
    var filesBackendCSS = walkSync("resources/sass/backend", sassEx);
    filesBackendCSS.forEach(function (filepath) {
        mix.sass(filepath, 'public/' + trimSass(filepath, "resources"));
    });
    //=======================Mix Backend SASS================================

    //=======================Mix Dev SASS================================
    var filesDevdCSS = walkSync("resources/sass/dev", sassEx);
    filesDevdCSS.forEach(function (filepath) {
        mix.sass(filepath, 'public/' + trimSass(filepath, "resources"));
    });
    //=======================Mix Dev SASS================================

    //=======================Mix Auth SASS================================
    var filesAuthCSS = walkSync("resources/sass/auth", sassEx);
    filesAuthCSS.forEach(function (filepath) {
        mix.sass(filepath, 'public/' + trimSass(filepath, "resources"));
    });
    //=======================Mix Auth SASS================================
}
//===========================SASS=========================================;