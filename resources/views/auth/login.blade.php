@extends('layouts.app')

@section('content')
<div class="login-wrapper ">
  <!-- START Login Background Pic Wrapper-->
  <div class="bg-pic">
    <!-- Alternative 1-->
    <!-- <img src="https://picsum.photos/700/300/?random" class="lazy"> -->
    <!-- Alternative 2 -->
    <img src="https://source.unsplash.com/random/1600x700" class="lazy">
    
  </div>
  <!-- END Login Background Pic Wrapper-->
  <!-- START Login Right Container-->
  <div class="login-container bg-white">
    <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
        <div class="text-center">
            <img src="{{ asset('img/dashi-logo.png') }}" alt="logo" data-src="{{ asset('img/dashi-logo.png') }}" data-src-retina="{{ asset('img/dashi-logo.png') }}" width="155" height="132">
        </div>
      <p class="p-t-20 text-center">Sign into your Dashi account or <a href="{{ url('/register') }}">Sign Up</a></p>
      <!-- START Login Form -->
      <form id="form-login" class="p-t-15" role="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <!-- START Form Control-->
        <div class="form-group form-group-default {{ $errors->has('email') ? ' has-error' : '' }}">
          <label>E-mail Address</label>
          <div class="controls">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
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
        <div class="row">
          <div class="col-md-6 no-padding sm-p-l-10">
            <div class="checkbox ">
              <input id="remember-me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              <label for="remember-me">Keep Me Signed in</label>
            </div>
          </div>
          <div class="col-md-6 d-flex align-items-center justify-content-end">
            <a href="{{ route('password.request') }}" class="text-info small">Forgot Your Password?</a>
          </div>
        </div>
        <!-- END Form Control-->
        <button class="btn btn-complete btn-cons btn-block m-t-20" type="submit">Log In</button>
      </form>
      <!--END Login Form-->
    </div>
  </div>
  <!-- END Login Right Container-->
</div>
@endsection
