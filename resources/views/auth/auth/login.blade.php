@extends('layouts.app')
@section('content')
    <div class="form-auth text-center auth">
        <div class="card shadow rounded-3">
            <div class="card-body">
                <h1 class="h3 mb-2 fw-normal name_unit">
                    {{ Str::upper(__('Educational Unit')) }}<br>{{ $currentEducationalUnit->full_name }}
                </h1>
                @if (session()->has('userError'))
                    <div class="alert alert-dismissible fade show m-0 p-0" role="alert">
                        <x-message-information colorBorder="danger" colorText="danger" sizeIcon="1.8em">
                            <div class="d-flex flex-colum">
                                <div class="lh-sm">
                                    {{ session('userError') }}
                                </div>
                                <div>
                                    <span class="fas fa-times" type="button" data-bs-dismiss="alert"
                                        aria-label="Close"></span>
                                </div>
                            </div>
                        </x-message-information>
                    </div>
                @endif
                <img class="mb-3" src="{{ $currentEducationalUnit->logoUrl() }}" width="120" height="135">
                <h2 class="h3 mb-3">{{ __('Log in') }}</h2>
                <form method="POST" action="{{ route('login') }}">

                    @csrf
                    <div class="form-floating mb-3">
                        <input id="email" name="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder="name@example.com"
                            value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <label for="email">
                            <i class="fas fa-user fa-sm"></i> {{ __('E-Mail Address') }}
                        </label>
                        @error('email')
                            <span class="invalid-feedback text-start" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="*******" required autocomplete="current-password">
                        <label for="password">
                            <i class="fas fa-key"></i> {{ __('Password') }}
                        </label>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="row mb-2">
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-12">
                            <div class="d-grid gap-2 col-8 mx-auto">
                                <button type="submit" class="w-100 btn btn-primary">
                                    <strong>{{ __('Login') }}</strong>
                                </button>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
