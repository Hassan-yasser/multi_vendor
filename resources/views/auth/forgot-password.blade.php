<x-guest-layout>
  <p class="login-box-msg mb-3">أدخل بريدك لإرسال رابط إعادة التعيين</p>

  <x-auth-session-status class="mb-3" :status="session('status')" />

  <form method="POST" action="{{ route('password.email') }}">
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
    <div class="d-grid gap-2 mb-2">
      <button type="submit" class="btn btn-primary">إرسال الرابط</button>
    </div>
  </form>

  <p class="mb-0">
    <a href="{{ route('login') }}">العودة لتسجيل الدخول</a>
  </p>
</x-guest-layout>
