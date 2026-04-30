<section>
  <header class="mb-4">
    <h2 class="h5 mb-1">{{ __('Profile Information') }}</h2>
    <p class="text-muted mb-0">{{ __('Update your account profile details.') }}</p>
  </header>

  <form method="post" action="{{ route('profile.update') }}" class="row g-3" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="col-md-6">
      <label class="form-label" for="first_name">{{ __('First name') }}</label>
      <input class="form-control" id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required>
      <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>

    <div class="col-md-6">
      <label class="form-label" for="last_name">{{ __('Last name') }}</label>
      <input class="form-control" id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required>
      <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>

    <div class="col-md-6">
      <label class="form-label" for="birthday">{{ __('Birthday') }}</label>
      <input class="form-control" id="birthday" name="birthday" type="date" value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}">
      <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
    </div>

    <div class="col-md-6">
      <label class="form-label" for="gender">{{ __('Gender') }}</label>
      <select id="gender" name="gender" class="form-select">
        <option value="">{{ __('Select gender') }}</option>
        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
        <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
      </select>
      <x-input-error class="mt-2" :messages="$errors->get('gender')" />
    </div>

    <div class="col-md-6">
      <label class="form-label" for="country">{{ __('Country') }}</label>
      <input class="form-control" id="country" name="country" type="text" value="{{ old('country', $user->country) }}">
      <x-input-error class="mt-2" :messages="$errors->get('country')" />
    </div>

    <div class="col-md-6">
      <label class="form-label" for="city">{{ __('City') }}</label>
      <input class="form-control" id="city" name="city" type="text" value="{{ old('city', $user->city) }}">
      <x-input-error class="mt-2" :messages="$errors->get('city')" />
    </div>

    <div class="col-md-8">
      <label class="form-label" for="address">{{ __('Address') }}</label>
      <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
      <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <div class="col-md-4">
      <label class="form-label" for="postal_code">{{ __('Postal code') }}</label>
      <input class="form-control" id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $user->postal_code) }}">
      <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
    </div>

    <div class="col-md-6">
      <label class="form-label" for="state">{{ __('State') }}</label>
      <input class="form-control" id="state" name="state" type="text" value="{{ old('state', $user->state) }}">
      <x-input-error class="mt-2" :messages="$errors->get('state')" />
    </div>

    @if ($user->store_id)
      <div class="col-12">
        <hr class="my-2" />
        <h3 class="h6">{{ __('Store appearance') }}</h3>
        <p class="text-muted small">{{ __('Logo and cover are stored under the profile folder on disk.') }}</p>
      </div>

      <div class="col-md-6">
        <x-forms.file
          name="logo"
          label="{{ __('Store logo') }}"
          :current-path="$user->store?->logo"
          :help="__('Optional. Replaces current logo — max 4 MB.')"
        />
      </div>

      <div class="col-md-6">
        <x-forms.file
          name="cover_image"
          label="{{ __('Store cover image') }}"
          :current-path="$user->store?->cover_image"
          :help="__('Optional banner image — max 8 MB.')"
        />
      </div>
    @endif

    <div class="col-12 d-flex align-items-center gap-3">
      <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
      @if (session('status') === 'profile-updated')
        <span class="text-success">{{ __('Saved successfully.') }}</span>
      @endif
    </div>
  </form>
</section>
