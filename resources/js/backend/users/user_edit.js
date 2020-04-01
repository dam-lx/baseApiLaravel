require('bootstrap-datepicker');
var toastr = require('toastr');
var $ = require('jquery');
$("#date").datepicker({
                          format        : "yyyy/mm/dd",
                          autoclose     : true,
                          todayHighlight: true
                      });
//upload image
function handleFileSelect(event) {
    var input = this;
    if (input.files && input.files.length) {
        var reader = new FileReader();
        this.enabled = false
        reader.onload = (function (e) {
            $(".thumb").attr('src', e.target.result);
        });
        reader.readAsDataURL(input.files[0]);
    }
}


$('#file').change(handleFileSelect);
//reset image
$(".reset_image").click(function(){
    _commonClearError();
    var src = $("img.thumb").data("src");
    $("img.thumb").attr('src', src);
    $('#file').val('');
});

//change Password
$(document).on("click","#changePass",function(){
    if($(this).is(":checked")){
        $(".password").removeClass("dis-none");
    }else{
        $(".password").addClass("dis-none");
    }
});

//=====================event edit account==============================
$(document).on("click",".edit_account",function (e) {
    var formData = new FormData();
    var image = $('input[type=file]')[0].files[0];
    image = (typeof(image) === "undefined")?"":image;
    formData.append("name", $("#name").val());
    formData.append("image", image);
    formData.append("date", $("#date").val());
    formData.append("gender", $('input[name=gender]:checked').val());
    formData.append("is_active", ($('input[name=active]:checked')?_isActive:_notActive));
    formData.append("oldImgSrc", $("img.thumb").data("path"));
    formData.append("email", $("#email").val());
    formData.append("id", $("#userId").val());
    if($("#changePass").is(":checked")){
        formData.append("password", $("#password").val());
        formData.append("password_confirmation", $("#password_confirmation").val());
        formData.append("changePass",1);
    }
    formData.append("role", $("#role").val());
    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: false,
        processData: false,
        url: "",
        data:formData,
        success: function (result) {
            if (result.status == _statusOK) {
                toastr.success(result.message, '',{timeOut: 2000});
                window.location.href= _routeListUser;
            }else{
                _commonShowError(result.data);
            }
        }
    });
})

//=====================event edit profile==============================
$(document).on("click",".edit_profile",function (e) {
    var formData = new FormData();
    var image = $('input[type=file]')[0].files[0];
    image = (typeof(image) === "undefined")?"":image;
    formData.append("name", $("#name").val());
    formData.append("image", image);
    formData.append("date", $("#date").val());
    formData.append("gender", $('input[name=gender]:checked').val());
    formData.append("oldImgSrc", $("img.thumb").data("path"));
    formData.append("email", $("#email").val());
    if($("#changePass").is(":checked")){
        formData.append("password", $("#password").val());
        formData.append("password_confirmation", $("#password_confirmation").val());
        formData.append("changePass",1);
    }
    $.ajax({
               type: 'POST',
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               contentType: false,
               processData: false,
               url: "",
               data:formData,
               success: function (result) {
                   if (result.status == _statusOK) {
                       toastr.success(result.message, '',{timeOut: 2000});
                       window.location.href= _routeListUser;
                   }else{
                       _commonShowError(result.data);
                   }
               }
           });
})

//=======================event reset form ========================
$(document).on("click","#reset",function () {
    _commonClearError();
    var old_src = $("#preview img").data("src");
    $("#preview img").attr("src",old_src);
    $(".password").addClass("dis-none");
    $('#file').val(''); 
    //==================reset gender=======================
    if(_gender == _male){
        $("#male").prop("checked",true).parent("label").addClass("active");
        $("#female").prop("checked",false).parent("label").removeClass("active");
    }else{
        $("#female").prop("checked",true).parent("label").addClass("active");
        $("#male").prop("checked",false).parent("label").removeClass("active");
    }

    //==================reset active==============================
    if(_active == _isActive){
        $("#active").prop("checked",true);
    }else{
        $("#active").prop("checked",false);
    }
})

//===========================event clear errors when input=================
$("#date").on("change",function(e){
    var key = $(".input-error#date").val();
    console.log(key);
    key = (typeof(key) !=='undefined')?key.length:0;
    if(key>0){
        $(".input-error#date").removeAttr("data-original-title");
        $(".input-error#date").removeClass("input-error");
    } });