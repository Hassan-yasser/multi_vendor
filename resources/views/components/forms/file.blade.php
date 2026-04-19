@props([
    'name',
    'label' => null,
    'required' => false,
    'help' => null,
    'currentPath' => null,
    'accept' => 'image/png,image/jpeg,image/jpg,image/webp,image/gif',
])

@php
  $fieldId = $attributes->get('id') ?? str_replace(['[', ']', '.'], '_', $name);
  $controlClass = 'form-control' . ($errors->has($name) ? ' is-invalid' : '');
  $previewUrl = $currentPath
      ? \Illuminate\Support\Facades\Storage::disk('public')->url($currentPath)
      : null;
@endphp

<div class="mb-3">
  @isset($label)
    <label for="{{ $fieldId }}" class="form-label">
      {{ $label }}
      @if ($required)
        <span class="text-danger" aria-hidden="true">*</span>
      @endif
    </label>
  @endisset

  @if ($previewUrl)
    <div class="mb-2">
      <img
        src="{{ $previewUrl }}"
        alt=""
        class="img-thumbnail d-block"
        style="max-height: 140px; width: auto"
      />
    </div>
  @endif

  <input
    type="file"
    name="{{ $name }}"
    id="{{ $fieldId }}"
    accept="{{ $accept }}"
    {{ $attributes->except(['id', 'name', 'accept', 'currentPath'])->merge(['class' => $controlClass]) }}
    @if ($required) required @endif
  />

  @isset($help)
    <div class="form-text">{{ $help }}</div>
  @endisset

  @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>
