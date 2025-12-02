@extends('layouts.app')

@section('content')
<div class="authincation h-100">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
                <div class="login-form">
                    <div class="text-center">
                        <h3 class="title">Sign In</h3>
                        <p>Sign in to your account to start using Dompact</p>
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="mb-1 text-dark">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="mb-4 position-relative">
                            <label class="mb-1 text-dark">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <span class="show-pass eye">
                                <i class="fa fa-eye-slash"></i>
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                            <div class="mb-4">
                                <div class="form-check custom-checkbox mb-3">
                                    <input type="checkbox" class="form-check-input" id="customCheckBox1">
                                    <label class="form-check-label mt-1" for="customCheckBox1">Remember my preference</label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <a href="javascript:;" class="btn-link text-primary">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="text-center mb-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="pages-left h-100">
                    <div class="login-content">
                        <img src="{{ asset('images/logo.png') }}" class="mb-3">
                    </div>
                    <div class="login-media text-center">
                        <img src="{{ asset('images/login.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
