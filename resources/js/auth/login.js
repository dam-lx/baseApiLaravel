window.$ = window.jQuery = require('jquery');
require('bootstrap');
require('jquery-toast-plugin');
import {notify} from '../lib/common_function';


//======================event forgot password ======================
$(document).on('click','.forgot_password',function (e) {
    e.preventDefault();
    notify('Warning', 'warning',"In progress", '#c99e17', '#9a7c17');
});

$(document).on("click","acv",function () {
    console.log("acv");
});
