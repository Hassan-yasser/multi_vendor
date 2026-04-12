<x-guest-layout>
  <p class="login-box-msg mb-3">New Password</p>

  <form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}" />

    <div class="input-group mb-3">
      <input
        type="email"
        name="email"
        value="{{ old('email', $email) }}"
        class="form-control @error('email') is-invalid @enderror"
        placeholder="Email"
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
        placeholder="New Password"
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
        placeholder="Confirm Password"
        required
        autocomplete="new-password"
      />
      <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
    </div>
    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-primary">Update Password</button>
    </div>
  </form>
</x-guest-layout>
