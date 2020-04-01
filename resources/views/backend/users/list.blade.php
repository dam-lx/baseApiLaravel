@extends("layouts.backend")
@push("css")
	<link rel="stylesheet" type="text/css" href="{{asset('css/backend/users/list_account.css')}}">
@endpush
@section("content")
<!--Title-->
<!--Table-->
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="tile">
			<div class="tile-title">
				<div class="row">
					<div class="col-md-9 col-sm-8">
						<button class="btn btn-primary" title="{{trans('backend.add_account')}}" data-toggle="tooltip" id="add">
							<i class="fa fa-plus"></i>
						</button>
						<button class="btn btn-danger" id="delete_all">{{trans('backend.delete_all')}}
						</button>
					</div>
					<div class="col-md-3 col-sm-4 t-r">
						<span class="display-title">
                                {{trans("label.display.title")}}
                            </span>
						<select class="form-control perpage" id="perPage">
							@foreach(\App\Core\Common\Pagging::ARR_PAGE as $obj)
								<option value="{{$obj}}" <?php if($perPage == $obj) echo "selected" ?>>
									{{$obj}}
								</option>
							@endforeach
						</select>
						<span class="display-unit">
                                {{trans("label.display.unit")}}
                            </span>
					</div>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-striped jambo_table table-hover table-user">
					<thead>
					<tr class="headings">
						<th style="text-align: center" class="animated-checkbox">
								<label>
									<input type="checkbox" id="check-all"><span class="label-text"></span>
								</label>
						</th>
						<th class="column-title">Image </th>
						<th class="column-title">Name </th>
						<th class="column-title">Birthdate</th>
						<th class="column-title">Gender </th>
						<th class="column-title">Email </th>
						<th class="column-title">Role </th>
						<th class="column-title">Active </th>
						<th class="column-title">Action </th>
					</tr>
					</thead>
					<!--Tbody-->
					<tbody id="tbody">
					</tbody>
				</table>
			</div>
		</div>
		<div  class="paginate-info row">
			<div class="page-count col-md-6"></div>
			<div id="pagination-demo" class="col-md-6"></div>
		</div>
	</div>
</div>
<!-- Template modal add-->
<div id="modal-add" class="dis-none"></div>
<div id="modal-edit" class="dis-none"></div>
@include("backend.users.templateUser")
@endsection
@push("js")
	<script>
		var _routeList           = "{{route('backend.user.list')}}";
		var _routeAdd            = "{{route('backend.user.add')}}";
		var _routeEdit           = "{{route('backend.user.edit')}}";
		var _routeDelete         = "{{route('backend.user.delete')}}";
		var _routePaginate       = "{{route('backend.user.paginate')}}";
		var _messageLoading      = "{{trans('backend.message_loading')}}";
		var _page                = "{{$page}}";
	</script>
<script src="{{asset('js/backend/users/list_account.js')}}"></script>
@endpush
