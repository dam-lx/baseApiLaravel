<!-- Bootstrap -->
<script>
    var _statusOK             = "{{\App\Core\Common\SDBStatusCode::OK}}";
    var _error                = "{{trans('common.error')}}";
    var _routeSwwitchLanguage = "{{route('backend.switch_language')}}";
    var _userId               = '{{$userInfor->id}}';
    var _textFirst            = "{{trans('label.first')}}";
    var _textPrev             = "{{trans('label.prev')}}";
    var _textNext             = "{{trans('label.next')}}";
    var _textLast             = "{{trans('label.last')}}";
    var _msgNothingDelete     = "{{trans('backend.msg_nothing_delete')}}";
    var _titleWarning         = "{{trans('backend.warning')}}";
    var _messageLoading       = "{{trans('backend.message_loading')}}";
    var _msgConfirmDeleteAll   = "{{trans('backend.msg_warning_deleteAll')}}";
    var _ok                   = "{{trans('common.ok')}}";
    var _edit                 = "{{trans('common.edit')}}";
    var _cancel               = "{{trans('common.cancel')}}";
    var _lang                 = "{{\Illuminate\Support\Facades\Session::get('locale',config('app.locale'))}}";
</script>
<script src="{{ asset('js/backend/layouts/layout_backend.js')}}"></script>
<script src="{{ asset('js/lib/common.js')}}"></script>
<script src="{{ asset('js/lib/pace.min.js')}}"></script>
<script>
    var CURRENT_URL   = window.location.href.split('#')[0].split('?')[0],
        $SIDEBAR_MENU = $('.app-menu');
    // check active menu
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').addClass('active');
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parents('.treeview').addClass('is-expanded');
    //set url for header
    $(".header-link").attr("href", CURRENT_URL);
    $(".header-title").text("{{trans("header.".\Illuminate\Support\Facades\Request::route()->getName())}}");
    // /Sidebar
    var randNum = function () {
        return (Math.floor(Math.random() * (1 + 40 - 20))) + 20;
    };

    function reinitTooltip() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    function customTooltip(target, tmp) {
        $(target).tooltip({template: tmp});
    }

    $(document).ready(function () {
        $('.logout-page').on('click', function () {
            $.ajax({
                       headers: {
                           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                       },
                       url    : "{{route('backend.dologout')}}",
                       type   : 'post',
                       success: function (data) {
                           window.location.href = "{{route('backend.login')}}";
                       },
                       error  : function (xhr, ajaxOptions, thrownError) {
                           console.log('Error ' + xhr.status + ' | ' + thrownError);
                       }
                   })
        })
    })
</script>
<!-- Custom Theme Scripts -->
@yield('lib_scripts')
<script src="{{ asset('js/lang/text.js')}}"></script>
@yield('form_scripts')
@stack("js")
