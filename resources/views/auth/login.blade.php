@extends("layouts.authen")
@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-form-title" style="background-image: url({{asset('common_images/login.png')}});">
                    <span class="login100-form-title-1">
                        {{trans('frontend.sign_in')}}
                    </span>
                </div>
                <form class="login100-form validate-form" method="POST" action="{{ route('backend.doLogin') }}">
                    @csrf
                    <div class="wrap-input100   m-b-26" data-validate="Email is required">
                    <!-- <span class="label-input100">{{trans('frontend.email_address')}}</span> -->
                        <input class="input100" type="email" name="email" placeholder="{{trans('placeholder.email')}}">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="wrap-input100  m-b-18" data-validate = "Password is required">
                    <!-- <span class="label-input100">{{trans('common.password')}}</span> -->
                        <input class="input100" type="password" name="password" placeholder="{{trans('placeholder.password')}}">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="flex-sb-m w-full p-b-30">
                        <div class="contact100-form-checkbox">
                            <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember_me">
                            <label class="label-checkbox100" for="ckb1">
                                {{trans('common.remember')}}
                            </label>
                        </div>

                        <div>
                            <a href="#" class="forgot_password">
                                {{trans('frontend.forgot_password')}} ?
                            </a>
                        </div>
                    </div>
                    <!--Show errors-->
                    @if(Session()->has('error'))
                        <div class="alert alert-danger alert-dismissible col-md-12">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{Session()->get('error')}}
                        </div>
                    @endif
                    <div class="error-area">
                        @if ($errors->has('email'))
                            <div class="alert alert-danger alert-dismissible col-md-12">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <span class="invalid-feedback"><strong>{!! nl2br($errors->first('email')) !!}</strong></span>
                            </div>
                        @endif
                        @if ($errors->has('password'))
                            <div class="alert alert-danger alert-dismissible col-md-12">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <span class="invalid-feedback"><strong>{!! nl2br($errors->first('password')) !!}</strong></span>
                            </div>
                        @endif
                        @if ($errors->has('message'))
                            <div class="alert alert-danger alert-dismissible col-md-12">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <span
                                    class="invalid-feedback"><strong>{!! nl2br($errors->first('message')) !!}</strong></span>
                            </div>
                        @endif
                    </div>
                    <div class="container-login100-form-btn ">
                        <button class="login100-form-btn">
                            {{trans('frontend.login')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
