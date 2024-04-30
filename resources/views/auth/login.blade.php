@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 mt-3">
            @if(config('app.demo_mode'))
                <div class="alert alert-warning text-center">
                    DEMO MODE<br>
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    @error('two_factor_code')
                        <div class="alert alert-danger" role="alert">
                            {{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ config('app.demo_mode') ? 'demo-admin@ship.town' : old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" value="{{ config('app.demo_mode') ? 'secret1144' : '' }}">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                                @if (Route::has('register'))
                                    <a class="btn btn-link" href="{{ route('register') }}">
                                        {{ __('Register') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>


{{--                @if(config('app.demo_mode'))--}}
{{--                    <div class="small mt-3">--}}
{{--                        Please use one of the following credentials to login. <br>--}}
{{--                        <br>--}}
{{--                        Demo User: <br>--}}
{{--                        email: <b>demo-user@ship.town</b> <br>--}}
{{--                        password: <b>secret1144</b> <br>--}}
{{--                        <br>--}}
{{--                        Demo Admin: <br>--}}
{{--                        email: <b>demo-admin@ship.town</b> <br>--}}
{{--                        password: <b>secret1144</b> <br>--}}
{{--                    </div>--}}
{{--                @endif--}}
        </div>
    </div>
</div>
@endsection
