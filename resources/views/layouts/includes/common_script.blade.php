<script>

    (function ($) {
        $.fn.ajax = function (options) {
            var defaults = {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: false,
                processData: false
            };
            $.extend(options,defaults);
            return $.ajax(options);
        }
    }(jQuery));

    /**
     * @author chieudv
     * @param type
     * @param textMessage
     * @param textHeading
     * @param redirect
     * @param url
     * @private
     */

    function _showProcessFinish(type,redirect,url,textMessage  ,textHeading) {
        if(redirect === undefined) {
            redirect = false;
        }
        if(url === undefined) {
            url = null;
        }
        if(textMessage === undefined) {
            textMessage = _messageTranslation[document.documentElement.lang].backend.successfulMessage;
        }
        if(textHeading === undefined) {
            textHeading = _messageTranslation[document.documentElement.lang].backend.successful;
        }

        $.toast({
            text: textMessage,
            heading: textHeading,
            icon: type,
            showHideTransition: 'plain',
            allowToastClose: true,
            hideAfter: 1000,
            stack: 5,
            position: 'top-right',
            textAlign: 'left',
            loader: true,
            loaderBg: '#9EC600',
            afterHidden: function () {
                if(redirect == true){
                    location.href = url
                }
            }
        });
    };

    /**
     * @author chieudv
     * @param areaSelector
     * @private
     */

    function _showProcessLoading(areaSelector) {
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        var classRan = "class_" + Math.floor(Math.random() * possible.length);
        if (areaSelector) {
            $(areaSelector).append("<div class='show-process " + classRan + " '><img alt='' src='{{ asset('common_images/loading.gif') }}' /></div>");
        } else {
            $('body').append("<div class='show-process-l " + classRan + " '><img alt='' src='{{ asset('common_images/loading.gif') }}' /></div>");
        }
        return classRan;
    }

    /**
     *
     * @param cl : là biến trả về của hàm _showProcessLoading
     * @private
     */
    function _hideProcessLoading(cl) {
        $("."+cl+"").remove();
    }
    function checkImg(files,classError,titleTrans,preview) {
        try {
            var exDefault = "{{ \App\Core\Common\UploadConst::FILE_IMAGE_UPLOAD_ACCESSED }}";
            var arrDefault = exDefault.split(',');
            for(var i=0;i<arrDefault.length;i++){
                if(files.name.toLowerCase().indexOf(arrDefault[i]) !== -1 && parseFloat(files.size/1024) <= parseFloat( {{ \App\Core\Common\UploadConst::BACKEND_UPLOAD_IMAGE_MAX }} )){
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



</script>
