<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex" style="height: 100vh;">
    <div class="container d-flex">
        <div class="card shadow rounded-3 m-auto">
            <div class="card-body">
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
</body>

</html>
