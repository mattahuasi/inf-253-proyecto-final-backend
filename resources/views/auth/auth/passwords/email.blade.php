@extends('layouts.app')
@section('content')
    <div class="form-auth auth">
        <div class="card shadow">
            <div class="card-header">{{ __('Reset Password') }}</div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success"
                         role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form action="{{ route('password.email') }}"
                      method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <input autocomplete="email"
                               autofocus
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               placeholder="name@example.com"
                               required
                               type="email"
                               value="{{ old('email') }}">
                        <label for="email">{{ __('E-Mail Address') }}</label>
                        @error('email')
                            <span class="invalid-feedback"
                                  role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button class="btn btn-primary w-100"
                            type="submit">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
