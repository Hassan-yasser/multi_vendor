<x-guest-layout variant="register">
  <p class="register-box-msg">تسجيل حساب جديد</p>

  <form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="input-group mb-3">
      <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        class="form-control @error('name') is-invalid @enderror"
        placeholder="الاسم"
        required
        autofocus
        autocomplete="name"
      />
      <div class="input-group-text"><span class="bi bi-person"></span></div>
      @error('name')
        <div class="invalid-feedback d-block w-100">{{ $message }}</div>
      @enderror
    </div>
    <div class="input-group mb-3">
      <input
        type="email"
        name="email"
        value="{{ old('email') }}"
        class="form-control @error('email') is-invalid @enderror"
        placeholder="البريد الإلكتروني"
        required
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
        autocomplete="new-password"
      />
      <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
      @error('password')
        <div class="invalid-feedback d-block w-100">{{ $message }}</div>
      @enderror
    </div>
    <div class="input-group mb-3">
      <input
        type="password"
        name="password_confirmation"
        class="form-control"
        placeholder="تأكيد كلمة المرور"
        required
        autocomplete="new-password"
      />
      <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
    </div>
    <div class="row">
      <div class="col-8">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="terms" required />
          <label class="form-check-label" for="terms">أوافق على الشروط</label>
        </div>
      </div>
      <div class="col-4">
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">تسجيل</button>
        </div>
      </div>
    </div>
  </form>

  <p class="mb-0 mt-3">
    <a href="{{ route('login') }}" class="text-center">لدي حساب بالفعل</a>
  </p>
</x-guest-layout>
