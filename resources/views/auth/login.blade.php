<x-guest-layout>
  <p class="login-box-msg">تسجيل الدخول</p>

  <x-auth-session-status class="mb-3" :status="session('status')" />

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="input-group mb-3">
      <input
        type="email"
        name="email"
        value="{{ old('email') }}"
        class="form-control @error('email') is-invalid @enderror"
        placeholder="البريد الإلكتروني"
        required
        autofocus
        autocomplete="username"
      />
      <div class="input-group-text"><span class="bi bi-envelope"></span></div>
      @error('email')
        <div class="invalid-feedback d-block w-100">{{ $message }}</div>
      @enderror
    </div>
    <div class="input-group mb-3">
      <input
        type="password"
        name="password"
        class="form-control @error('password') is-invalid @enderror"
        placeholder="كلمة المرور"
        required
        autocomplete="current-password"
      />
      <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
      @error('password')
        <div class="invalid-feedback d-block w-100">{{ $message }}</div>
      @enderror
    </div>
    <div class="row">
      <div class="col-8">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" />
          <label class="form-check-label" for="remember">تذكرني</label>
        </div>
      </div>
      <div class="col-4">
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">دخول</button>
        </div>
      </div>
    </div>
  </form>

  <p class="mb-1 mt-3">
    @if (Route::has('password.request'))
      <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
    @endif
  </p>
  <p class="mb-0">
    <a href="{{ route('register') }}" class="text-center">إنشاء حساب جديد</a>
  </p>
</x-guest-layout>
