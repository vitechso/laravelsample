@extends('auth.layouts.auth')



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Poppins&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/front/css/style.css') }}">



@section('body_class','login')



<?php $logo1 = websitelogo1();



$logo1 = $logo1[0]->logo1; ?>



@section('content')



<style type="text/css">



    .logoimg img {



    width: 160px;



    height: 100%;



    object-fit: cover;



}



.logoimg {



    text-align: center;



}



.login_content{



    padding-top: 0;



}

.login-checkbox-block {
    display: flex;
    color: #fff;
}

.login-checkbox-block label {
    display: flex;
    align-items: center;
}
.login-checkbox-block span {
    font-size: 12px;
    color: #D9D9D9;
}

</style>

    

    <section class="login-section">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="login-block">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-lg-8">
                                <div class="logo-block pb-5">
                                    @php
                                      $logo1="../front/svg/pp-logo.svg";
                                    @endphp
                                    <img src="{{imagefolderpath($logo1)}}"/>
                                </div>
                        <section class="login_content">
                            {{ Form::open(['route' => 'login']) }}
                                <!-- <h1>{{ __('views.auth.login.header') }}</h1> -->
                                <div class="form-group adjust-form">
                                    <input id="email" type="email" class="form-control custom-input custom-color" name="email" value="{{ old('email') }}" placeholder="{{ __('views.auth.login.input_0') }}" required autofocus>
                                </div>
                                <div class="form-group adjust-form">
                                    <input id="password" type="password" class="form-control custom-input custom-color" name="password" placeholder="{{ __('views.auth.login.input_1') }}" required>
                                </div>
                                <!-- <div class="checkbox al_left">
                                    <label class="text-white">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('views.auth.login.input_2') }}
                                    </label>
                                </div> -->

                                <div class="custom-control custom-checkbox login-checkbox-block">
                                    <input type="hidden" name="remember" class="custom-control-input" value="46">
                                    <input type="checkbox" name="remember" class="custom-control-input" id="login-check" value="75" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="login-check"><span class="">{{ __('views.auth.login.input_2') }}</span></label>
                                </div>

                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if (!$errors->isEmpty())
                                    <div class="alert alert-danger" role="alert">
                                        {!! $errors->first() !!}
                                    </div>
                                @endif
                                <!-- <div>
                                    <button class="btn custom-btn check-main" type="submit">{{ __('views.auth.login.action_0') }}</button>
                                    <a class="reset_pass" href="{{ route('password.request') }}">
                                        {{ __('views.auth.login.action_1') }}
                                    </a>
                                </div> -->

                                <div class="btn-block mt-3 d-flex justify-content-center">
                                        <button type="submit" class="btn custom-btn check-main">
                                            <span class="check">
                                                <i class="fas fa-check"></i>
                                            </span>{{ __('views.auth.login.action_0') }}
                                        </button>
                                    </div>

                                <div class="clearfix"></div>
                               <!--  @if(config('auth.users.registration'))
                                    <div class="separator">
                                        <p class="change_link">{{ __('views.auth.login.message_1') }}
                                            <a href="{{ route('register') }}" class="to_register"> {{ __('views.auth.login.action_2') }} </a>
                                        </p>
                                        <div class="clearfix"></div>
                                        <br/>
                                    </div>
                                @endif -->
                            {{ Form::close() }}
                        </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection







@section('styles')



    @parent







    {{ Html::style(mix('assets/auth/css/login.css')) }}



@endsection