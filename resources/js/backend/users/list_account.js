// Initialise imported function as jQuery function
var $ = jQuery = require('jquery');
require('jquery-confirm/js/jquery-confirm');
require('twbs-pagination');
import {buildList, buildPaginate, clearStoragePage, saveStoragePage} from "../../lib/common_function";

require('jquery-toast-plugin');

var toastr     = require('toastr');
//=====================variables================================
var _isLoading = 0;

//============================event load page=======================
$(document).ready(function () {
    getList(_page);
});

//============================event filter=======================
$(document).on('click', '#btn_filter', function () {
    getList(1, true);
});

//============================event change page display=======================
$(document).on("change","#perPage",function () {
    getList(_page,true);
});


//==============================event add account ==========================
// $(document).on("click", "#add", function () {
//     var pathname = window.location.search;
//     saveStoragePage("path_param", pathname);
//     window.location.href = _routeAdd;
// });


//==============================event edit account ==========================
$(document).on("click", ".edit ", function () {
    var path_param = window.location.search;
    (path_param!="")?saveStoragePage("path_param", path_param):clearStoragePage("path_param");
    window.location.href = _routeEdit + "/" + $(this).data('id');
});

//======================event delete account =========================
$(document).on("click", ".delete", function (e) {
    var arrOldImage = [], arrId = [];
    arrId.push($(this).data("id"));
    arrOldImage.push($(this).parents("tr").attr('avatar'));
    $.confirm({
                  title         : _titleWarning,
                  icon          : 'fa fa-exclamation-circle',
                  boxWidth      : '30%',
                  useBootstrap  : false,
                  type          : "orange",
                  closeIcon     : true,
                  closeIconClass: 'fa fa-close',
                  content       : _msgConfirmDelete,
                  buttons       : {
                      Save  : {
                          text    : _ok,
                          btnClass: 'btn btn-primary',
                          action  : function () {
                              $.ajax({
                                         headers   : {
                                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                         },
                                         url       : _routeDelete,
                                         type      : 'POST',
                                         data      : {arrId: arrId, arrOldImage: arrOldImage},
                                         beforeSend: function () {
                                             if (_isLoading == 0)
                                             {
                                                 _isLoading = 1;
                                             } else
                                             {
                                                 notify(_error, 'error', _messageLoading, '#AA3131', '#792A2A');
                                                 return false;
                                             }
                                         },
                                         success   : function success(data) {
                                             if (data.status == _statusOK)
                                             {
                                                 _isLoading = 0;
                                                 toastr.success(data.message, '', {timeOut: 2000});
                                                 getList(_page);
                                             } else
                                             {
                                                 notify(_error, 'error', data.message, '#AA3131', '#792A2A');
                                             }
                                         },
                                         error     : function error(xhr, ajaxOptions, thrownError) {
                                             console.log('Error ' + xhr.status + ' | ' + thrownError);
                                         }
                                     });
                          }
                      },
                      cancel: {
                          text    : _cancel,
                          btnClass: 'btn btn-default',
                          action  : function () {
                          }
                      }
                  }
              });
});

//=====================event check all================================
$(document).on("change", "#check-all", function (e) {
    var checkboxes = $(this).closest('table').find(':checkbox');
    checkboxes.prop('checked', $(this).is(':checked'));
});

//====================event delete all account =======================
$(document).on("click", "#delete_all", function (e) {
    var checked = $("input:checked").length;
    if (checked === 0)
    {
        $.alert({
                    title   : _titleWarning,
                    icon    : 'fa fa-exclamation-circle',
                    type    : "orange",
                    boxWidth: '20%',
                    content : '<span style="font-size: 16px">' + _msgNothingDelete + '</span>',
                    buttons       : {
                        Save: {
                            text: _ok
                        }
                    }
                });
    } else
    {
        $.confirm({
                      title         : _titleWarning,
                      icon          : 'fa fa-exclamation-circle',
                      boxWidth      : '30%',
                      useBootstrap  : false,
                      type          : "orange",
                      closeIcon     : true,
                      closeIconClass: 'fa fa-close',
                      content       : _msgConfirmDeleteAll,
                      buttons       : {
                          Save  : {
                              text    : _ok,
                              btnClass: 'btn btn-primary',
                              action  : function () {
                                  var arrId = [], arrOldImage = [];
                                  $(".check-delete input:checked").each(function () {
                                      arrId.push($(this).val());
                                      arrOldImage.push($(this).parents("tr").attr('avatar'));
                                  });
                                  $.ajax({
                                             headers   : {
                                                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                             },
                                             url       : _routeDelete,
                                             type      : 'POST',
                                             data      : {arrId: arrId, arrOldImage: arrOldImage},
                                             beforeSend: function () {
                                                 if (_isLoading == 0)
                                                 {
                                                     _isLoading = 1;
                                                 } else
                                                 {
                                                     notify(_error, 'error', _messageLoading, '#AA3131', '#792A2A');
                                                     return false;
                                                 }
                                             },
                                             success   : function success(data) {
                                                 if (data.status == _statusOK)
                                                 {
                                                     _isLoading = 0;
                                                     toastr.success(data.message, '', {timeOut: 2000});
                                                     getList(_page);
                                                 } else
                                                 {
                                                     notify(_error, 'error', data.message, '#AA3131', '#792A2A');
                                                 }
                                             },
                                             error     : function error(xhr, ajaxOptions, thrownError) {
                                                 console.log('Error ' + xhr.status + ' | ' + thrownError);
                                             }
                                         });
                              }
                          },
                          cancel: {
                              text    : _cancel,
                              btnClass: 'btn btn-default',
                              action  : function () {
                              }
                          }
                      }
                  });
    }
});

//=====================function getList===============================
function getList(page, search) {
    var param = {}, strParam = "", routePage;
    // ($("#key").val() != "") ? param.key = $("#key").val() : "";
    param.perPage = $("#perPage").val();
    strParam = $.param(param);
    if (typeof (search) !== 'undefined')
    {
        var url = (strParam != "") ? _routeList + "?" + strParam + "&page=" + page : _routeList + "?page=" + page;
        window.history.replaceState(null, null, url);
    }
    var map_field = {
        'img'      : 'src',
        'name'     : 'name',
        'birthdate': 'birth_date',
        'gender'   : 'gender',
        'email'    : 'email',
        'role'     : 'role',
        'active'   : 'active',
    };
    routePage     = (strParam != "") ? _routePaginate + "?" + strParam : _routePaginate;
    $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url    : routePage,
               data   : {page: page},
               type   : 'GET',
               success: function (data) {
                   if (data.status == _statusOK)
                   {
                       buildList(data, map_field,strParam,"user");
                       buildPaginate('#pagination-demo', page, data.data.last_page, routePage, strParam, map_field,"user");
                   } else
                   {
                       notify(_error, 'error', data.message, '#AA3131', '#792A2A');
                   }
               },
               error  : function error(xhr, ajaxOptions, thrownError) {
                   console.log('Error ' + xhr.status + ' | ' + thrownError);
               }
           });
}

