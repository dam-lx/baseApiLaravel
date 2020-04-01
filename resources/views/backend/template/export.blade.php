@extends("layouts.backend")
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h2 class="mb-3 line-head" id="typography">
                            Import Template (excel):
                            <a class="pull-right btn btn-primary icon-btn" href="https://docs.laravel-excel.com/3.1/getting-started/" target="_blank"><i class="fa fa-file"></i>Docs</a>
                        </h2>
                    </div>
                </div>
                <form action="{{ route('doImport_template') }}" class="form-horizontal" method="post"
                      enctype="multipart/form-data">
                    <div>
                        {{csrf_field()}}
                        <input type="file" class="form-control col-md-5" name="imported_file"/>
                    </div>
                    <br>
                    <button id="import" class="btn btn-primary">Import</button>
                </form>
                <div>Export From Template (excel):</div>
                <button id="export" class="btn btn-primary">Export</button>
                <br><br>
                <div>Export From Template (csv):</div>
                <button id="exportCommon" class="btn btn-primary">Export CSV</button>
                <br><br>
                @endsection
            </div>
        </div>
    </div>
@section('form_scripts')
    <script>

        $(document).ready(function () {

            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });

            $(document).on('click','#export',function () {
                location.href = "{{ route('doExports_template') }}";
            });
            $(document).on('click','#exportCommon',function () {
                location.href = "{{ route('doExportsCommon_template',['type'=>'csv']) }}";
            });

        });
    </script>
@endsection



