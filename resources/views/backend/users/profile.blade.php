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
                        <input id="userId" name="userId" type="hidden" class="form-control" value="{{$user->id}}"/>
                        <div id="preview">
                            <img class="thumb" title="avatar" data-path="{{$user->avatar}}" data-src="{{asset(\App\Core\Helpers\CommonHelper::getImageSrc($user->avatar))}}" src="{{asset(\App\Core\Helpers\CommonHelper::getImageSrc($user->avatar))}}">
                            <span class="reset_image"><i class="fa fa-close"></i> </span>
                        </div>

                    </div>
                </div>
                <div class="tile-footer">

                    <label for="file" class="custom-file-upload btn btn-outline-secondary camera" name="image">
                        <i class="fa fa-picture-o"></i> {{trans('label.change_avatar')}}
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
                                    <input class="form-control" name="name" id="name" type="text" placeholder="{{trans('placeholder.backend.name')}}" value="{{$user->name}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label ">{{trans('label.birthdate')}}</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar-o"></i> </span></div>
                                    <input class="form-control" type="text" name="date" id="date" placeholder=" {{trans('placeholder.backend.birthdate')}}" value="{{$user->birth_date}}">
                                </div>
                            </div>
                        </div>
                        <?php
                        if($user->gender ==\App\Core\Common\UserConst::male ){
                            $male = "checked";
                            $class_male = "active";
                            $female = "";
                            $class_feMale = "";
                        }else{
                            $male = "";
                            $class_male = "";
                            $female = "checked";
                            $class_feMale = "active";
                        }
                        ?>
                        <div class="form-group">
                            <label class="control-label ">{{trans('label.gender')}}</label>
                            <div class="form-group">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-primary {{$class_male}}" >
                                        <input class="radio-gender"  type="radio" name="gender" value="1" id="male" autocomplete="off" {{$male}}> {{trans('label.male')}} &nbsp;
                                    </label>
                                    <label class="btn btn-primary {{$class_feMale}}">
                                        <input class="radio-gender"  type="radio" name="gender" value="0"  autocomplete="off" id="female" {{$female}}> {{trans('label.female')}} &nbsp;
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label ">{{trans('label.email')}}</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-envelope-o"></i> </span></div>
                                    <input class="form-control" id="email" value="{{$user->email}}"
                                           placeholder="{{trans('placeholder.backend.email')}}" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="animated-checkbox">
                            <label>
                                <input type="checkbox" name="changPass" id="changePass">
                                <span class="label-text">{{trans('label.changePass')}}</span>
                            </label>
                        </div>
                        <div class="form-group password dis-none">
                            <label class="control-label ">{{trans('label.password')}}</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i> </span></div>
                                    <input class="form-control" type="password"  id="password" name="password" placeholder="{{trans('placeholder.backend.password')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group password dis-none">
                            <label class="control-label ">{{trans('label.rePassword')}}</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-lock"></i> </span></div>
                                    <input class="form-control" type="password"  id="password_confirmation" name="password_confirmation" placeholder="{{trans('placeholder.backend.rePassword')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{trans('label.active')}}</label>
                            <div class="form-group">
                                <div class="input-group toggle lg">
                                    <label  class="toggle-flip">
                                        <input type="checkbox" id="active" @if($user->is_active == \App\Core\Common\UserConst::active) {{"checked"}} @endif disabled=""><span class="flip-indecator" data-toggle-on="{{trans('label.on')}}" data-toggle-off="{{trans('label.of')}}" ></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="tile-footer">
                            <div class="row">
                                <div class="col-md-12 col-md-offset-3 t-r">
                                    <button type="button" class="btn btn-success edit_profile">{{trans('common.save')}}</button>
                                    <button id="reset" class="btn btn-primary" type="reset">{{trans('common.reset')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@push("js")
    <script>
        var _routeListUser = '{{route("backend.user.list")}}';
        var _gender        = "{{$user->gender}}";
        var _male          = '{{\App\Core\Common\UserConst::male}}';
        var _notActive     = "{{\App\Core\Common\UserConst::notActive}}";
        var _isActive      = "{{\App\Core\Common\UserConst::active}}";
    </script>
    <script src="{{asset('js/backend/users/user_edit.js')}}"></script>
@endpush



