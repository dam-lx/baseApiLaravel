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

//=======================event add==============================================
$(document).on("click",".add",function (e) {
    let formData = new FormData();
    let image = $('input[type=file]')[0].files[0];
    image = (typeof(image) === "undefined")?"":image;
    formData.append("name", $("#name").val());
    formData.append("image", image);
    formData.append("date", $("#date").val());
    formData.append("gender", $('input[name=gender]:checked').val());
    formData.append("email", $("#email").val());
    formData.append("password", $("#password").val());
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
});

//=======================event reset form ========================
$(document).on("click","#reset",function () {
    _commonClearError();
    var old_src = $("#preview img").data("src");
    $("#preview img").attr("src",old_src);
    $(".password").css("display","none");
    $('#file').val('');
    //==================reset gender=======================
    $("input[name=gender]").prop("checked",false).parent('label').removeClass("active");
    $("#male").prop("checked",true).parent('label').addClass("active");
});

//===========================event clear errors when input=================
$("#date").on("change",function(e){
    var key = $(".input-error#date").val();
    console.log(key);
    key = (typeof(key) !=='undefined')?key.length:0;
    if(key>0){
        $(".input-error#date").removeAttr("data-original-title");
        $(".input-error#date").removeClass("input-error");
    } });