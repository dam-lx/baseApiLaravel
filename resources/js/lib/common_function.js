//==================================function notify===========================
var $ = require('jquery');

export function notify(headingContent, icon, content, bgColor, loaderBg) {
    if(bgColor==null||loaderBg==null){
        switch (icon)
        {
            case "success":{
                bgColor = "#437F2C";
                loaderBg = "#3E943C";
                break;
            }
            case "warning":{
                bgColor = "#c99e17";
                loaderBg = "#9a7c17";
                break;
            }
            case "error":{
                bgColor = "#AA3131";
                loaderBg = "#792A2A";
                break;
            }
        }
    }
    $.toast({
                text              : content,
                heading           : headingContent,
                icon              : icon,
                showHideTransition: 'plain',
                allowToastClose   : true,
                hideAfter         : 5000,
                bgColor           : bgColor,
                stack             : 5,
                position          : 'top-right',
                textAlign         : 'left',
                loader            : true,
                loaderBg          : loaderBg
            });
}

/**
 * @param data
 * @param map_field
 * VD: map_field => {'class' : 'field_name'}
 * contener phải có id là #tbody
 */

export function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};



export function buildList(data, map_field,strParam,type="default") {
    console.log("Default");
    var tbody = $("#tbody");
    $(tbody).empty();
    if (typeof (data.data.data) === 'undefined' || data.data.data.length <= 0)
    {
        showEmpty(data,map_field,strParam,type);
    }else{
        $(".page-count").text("全"+data.data.total+"件中、"+data.data.from+"～"+data.data.to+"件を表示");
        var item = data.data.data;
        //build
        item.forEach(function (obj) {
            var row = $("#data-content").contents().clone();
            $.each(map_field, function (item_class, field_name) {
                var col = $(row).find('.' + item_class);
                $(row).attr('id', obj.id);
                if (col.hasClass('img'))
                {
                    col.attr({'src': obj.src, 'avatar': obj.avatar});
                } else
                {
                    col.html(obj[field_name]);
                }
            });

            //edit
            // var route_edit = $(row).find('.edit').attr("data-route");
            $(row).find('.edit').attr("data-id", obj.id);
            //delete
            $(row).find('.delete').attr("data-id", obj.id);
            $(row).find('.check-item').val(obj.id);
            if (obj.userId == _userId)
            {
                $(row).find('.delete').remove();
            }
            $(tbody).append($(row));
        });
        reinitTooltip();
    }
}

export function showEmpty(data,map_field,strParam,type="default") {
    $(".page-count").text("");
    var pathname = window.location.pathname;
    if (_page > 1)
    {
        _page = data.data.last_page;
        window.location.href = (strParam != "")? pathname + "?" + strParam + "&page=" + _page:pathname + "?page=" + _page;
    } else
    {
        let tmp_empty;
        if(strParam != ""){
            tmp_empty = "<tr class='empty-data'><td colspan='"+(Object.keys(map_field).length+2)+"'>"+_messageTranslation[_lang]["backend"][type]['emptySearch']+"</td></tr>";
        }else{
            tmp_empty = "<tr class='empty-data'><td colspan='"+(Object.keys(map_field).length+2)+"'>"+_messageTranslation[_lang]["backend"][type]['emptyData']+"</td></tr>";
        }
        $(tbody).append($(tmp_empty));
    }
}

export function buildPaginate(target, startPage, total_pages, urlPage, strParam, map_field,type = "default",callback) {
    $(target).twbsPagination('destroy');
    $(target).twbsPagination({
                                 hideOnlyOnePage       : true,
                                 startPage             : parseInt(startPage),
                                 initiateStartPageClick: false,
                                 totalPages            : parseInt(total_pages),
                                 visiblePages          : 8,
                                 first                 : _textFirst,
                                 prev                  : _textPrev,
                                 next                  : _textNext,
                                 last                  : _textLast,
                                 onPageClick           : function (event, page, total_page) {
                                     if (parseInt(total_pages) != 1)
                                     {
                                         let pathname = window.location.pathname;
                                         if (strParam != "")
                                         {
                                             window.history.replaceState(null, null, pathname + "?" + strParam + "&page=" + page);
                                         } else
                                         {
                                             window.history.replaceState(null, null, pathname + "?page=" + page);
                                         }
                                         _page = page;
                                         $("#check-all").prop('checked', false);//set check-all = false
                                         $.ajax({
                                                    headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                                    url     : urlPage,
                                                    type    : 'GET',
                                                    data    : {page: page},
                                                    success : function (data) {
                                                        if (data.status == _statusOK)
                                                        {
                                                            (callback)?callback(data, map_field,strParam):buildList(data, map_field,strParam,type);
                                                        } else
                                                        {
                                                            notify(_error, 'error', data.message, '#AA3131', '#792A2A');
                                                        }
                                                    },
                                                    error: function error(xhr, ajaxOptions, thrownError) {
                                                        console.log('Error ' + xhr.status + ' | ' + thrownError);
                                                    }
                                                });
                                     }
                                 }
                             });
}

export function saveStoragePage(key, value) {
    sessionStorage.setItem(key, value);
}

export function clearStoragePage(key) {
    sessionStorage.removeItem(key);
}

export function getStoragePage(key) {
    if (sessionStorage.getItem(key))
    {
        return sessionStorage.getItem(key);
    }
    return "";
}

export function checkImg(files,classError,titleTrans) {
    try {
        var exDefault = _exDefault;
        var arrDefault = exDefault.split(',');
        for(var i=0;i<arrDefault.length;i++){
            if(files.name.toLowerCase().indexOf(arrDefault[i]) !== -1 && parseFloat(files.size/1024) <= parseFloat( _maxSizeFile )){
                $(classError).removeClass('input-file-error');
                $(classError).removeAttr('title');
                $(classError).removeAttr('data-title');
                return true;
            }else{
                $(classError).addClass('input-file-error');
                $(classError).attr("title",titleTrans);
                $(classError).attr("data-title",'error');
                continue;
            }
        }
    }catch (e) {
        console.log(e);
    }
}


