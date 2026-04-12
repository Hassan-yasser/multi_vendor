<x-guest-layout>
  <p class="mb-3">منطقة محمية. أكد كلمة المرور للمتابعة.</p>

  <form method="POST" action="{{ route('password.confirm') }}">
    @csrf
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
    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">تأكيد</button>
    </div>
  </form>
</x-guest-layout>
