@extends('layouts.backend')
@push("css")
	<link href="{{ asset('css/backend/users/user_add.css')}}" rel="stylesheet">
@endpush
@section('content')
	<div class="row">
		<div class="col-md-6 div_avatar">
			<div class="tile">
				<h3 class="tile-title">{{trans('backend.user_avatar')}}</h3>
				<div class="tile-body">
					<div class="form-group">
						<input id="file" name="image" type="file" class="form-control"/>
						<div id="preview">
							<img class="thumb" title="avatar" data-src="{{asset('common_images/no-avatar.png')}}"
								 src="{{asset('common_images/no-avatar.png')}}">
							<span class="reset_image"><i class="fa fa-close"></i> </span>
						</div>

					</div>
				</div>
				<div class="tile-footer">

					<label for="file" class="custom-file-upload btn btn-outline-secondary camera" name="image">
						<i class="fa fa-picture-o"></i> {{trans('label.add_avatar')}}
					</label>
				</div>
			</div>
		</div>
		<div class="col-md-6 div_info">
			<div class="tile">
				<h3 class="tile-title">{{trans('backend.user_info')}}</h3>
				<div class="tile-body">
					<form class="form-horizontal">
						<div class="form-group">
							<label class="control-label ">{{trans('label.name')}}</label>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user"></i> </span></div>
									<input class="form-control" name="name"
										   id="name" type="text" placeholder="{{trans('placeholder.backend.name')}}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label ">{{trans('label.birthdate')}}</label>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar-o"></i> </span></div>
									<input class="form-control" type="text" name="date" id="date" placeholder="{{trans('placeholder.backend.birthdate')}}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label ">{{trans('label.gender')}}</label>
							<div class="form-group">
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn btn-primary active">
										<input class="radio-gender"  type="radio" name="gender" value="1" id="male" autocomplete="off" checked=""> {{trans('label.male')}} &nbsp;
									</label>
									<label class="btn btn-primary">
										<input class="radio-gender"  type="radio" name="gender" value="0"  autocomplete="off" id="female"> {{trans('label.female')}} &nbsp;
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label ">{{trans('label.email')}}</label>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-envelope-o"></i> </span></div>
									<input class="form-control" id="email"
										   placeholder="{{trans('placeholder.backend.email')}}" name="email">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label ">{{trans('label.password')}}</label>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i> </span></div>
									<input class="form-control" type="password"  id="password" name="password" placeholder="{{trans('placeholder.backend.password')}}">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label ">{{trans('label.role')}}</label>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-user-secret"></i> </span></div>
									<select class="form-control has-feedback-left" id="role" name="role">
										<option value="">--Choose Role--</option>
										@foreach($arrRole as $role)
											<option value="{{$role->role_value}}">
												{{$role->name}}
											</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-12 col-md-offset-3">
							<button type="button" class="btn btn-success add">{{trans('common.save')}}</button>
							<button id="reset" class="btn btn-primary" type="reset">{{trans('common.reset')}}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@push("js")
	<script>
		var _routeListUser = '{{route("backend.user.list")}}';
	</script>
	<script src="{{asset('js/backend/users/user_add.js')}}"></script>
@endpush



