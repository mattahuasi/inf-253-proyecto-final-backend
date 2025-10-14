@extends('layouts.app')

@section('content')
    <div class="form-auth auth">
        <div class="card shadow">
            <div class="card-header">{{ __('Reset Password') }}</div>
            <div class="card-body">
                <form action="{{ route('password.update') }}"
                      method="POST">
                    @csrf

                    <input name="token"
                           type="hidden"
                           value="{{ $token }}">
                    <div class="form-floating mb-3">
                        <input autocomplete="email"
                               autofocus
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               placeholder="name@example.com"
                               required
                               type="email"
                               value="{{ $email ?? old('email') }}">
                        <label for="email">
                            {{ __('E-Mail Address') }}
                        </label>
                        @error('email')
                            <span class="invalid-feedback"
                                  role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input autocomplete="new-password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required
                               type="password">
                        <label for="password">
                            {{ __('Password') }}
                        </label>
                        @error('password')
                            <span class="invalid-feedback"
                                  role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input autocomplete="new-password"
                               class="form-control"
                               id="password-confirm"
                               name="password_confirmation"
                               placeholder="Confirm Password"
                               required
                               type="password">
                        <label for="password-confirm">
                            {{ __('Confirm Password') }}
                        </label>
                    </div>
                    <button class="btn btn-primary w-100"
                            type="submit">
                        {{ __('Reset Password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
