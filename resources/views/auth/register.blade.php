@extends('layouts.app')

@section('content')
<div class="login-wrapper ">
  <!-- START Login Background Pic Wrapper-->
  <div class="bg-pic">
    <img class="auth-bg" src="" class="lazy">
  </div>
  <!-- END Login Background Pic Wrapper-->
  <!-- START Login Right Container-->
  <div class="login-container bg-white">
    <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
        <div class="text-center">
            <img src="{{ asset('img/dashi-logo.png') }}" alt="logo" data-src="{{ asset('img/dashi-logo.png') }}" data-src-retina="{{ asset('img/dashi-logo.png') }}" width="155" height="132">
        </div>
      <p class="p-t-20 text-center">Sign up</p>
      <!-- START Login Form -->
      <form id="form-signup" class="p-t-15" role="form" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}
        <!-- START Form Control-->
        <div class="form-group form-group-default {{ $errors->has('name') ? ' has-error' : '' }}">
          <label>Name</label>
          <div class="controls">
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
          </div>
        </div>
        <!-- END Form Control-->
        <!-- START Form Control-->
        <div class="form-group form-group-default {{ $errors->has('email') ? ' has-error' : '' }}">
          <label>E-Mail Address</label>
          <div class="controls">
            <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required>
            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
          </div>
        </div>
        <!-- END Form Control-->
        <!-- START Form Control-->
        <div class="form-group form-group-default {{ $errors->has('password') ? ' has-error' : '' }}">
          <label>Password</label>
          <div class="controls">
            <input id="password" type="password" class="form-control" name="password" required>
            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
          </div>
        </div>
        <!-- START Form Control-->
        <!-- START Form Control-->
        <div class="form-group form-group-default">
          <label>Confirm Password</label>
          <div class="controls">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
          </div>
        </div>
        <!-- START Form Control-->
        <!-- END Form Control-->
        <button class="btn btn-complete btn-cons btn-block m-t-20" type="submit">Sign Up</button>
      </form>
      <!--END Login Form-->
    </div>
  </div>
  <!-- END Login Right Container-->
</div>
@endsection
