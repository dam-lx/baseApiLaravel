@extends("layouts.backend")
@push("css")
    <link href="{{asset('css/backend/includes/form_notify.css')}}">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Bootstrap Notify</h3>
                    <p><a class="btn btn-primary icon-btn" href="http://bootstrap-notify.remabledesigns.com/" target="_blank"><i class="fa fa-file"></i>Docs</a></p>
                </div>
                <div class="tile-body">
                    <p>This plugin can be used to notify user about status of some action which he has performed.</p>
                    <h4>Demo</h4><a class="btn btn-info" id="demoNotify" href="#">Sample Notification</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">SweetAlert</h3>
                    <p><a class="btn btn-primary icon-btn" href="https://sweetalert2.github.io/" target="_blank"><i class="fa fa-file"></i>Docs</a></p>
                </div>
                <div class="tile-body">
                    <p>This plugin can be used as the replacement of native javascript alert, confirm and prompt functions.</p>
                    <h4>Demo</h4><a class="btn btn-info" id="demoSwal" href="#">Sample Alert</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("js")
    <script src="{{asset('js/backend/includes/form_notify.js')}}"></script>
@endpush