window.$ = window.jQuery = require('jquery');
require('bootstrap');
require('./main');
import {notify} from '../../lib/common_function';
window.notify = notify;

//===========================event change language=========================
$(document).on('click','.change_language',function(){
    var language = $(this).data('language');
    $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url:_routeSwwitchLanguage,
               type: 'POST',
               data:{language:language},
               success: function success(data) {
                   if(data.status == _statusOK){
                       location.reload();
                   }else{
                       notify(_error, 'error', data.message, '#AA3131', '#792A2A');
                   }
               },
               error: function error(xhr, ajaxOptions, thrownError) {
                   console.log('Error ' + xhr.status + ' | ' + thrownError);
               }
           });
});
