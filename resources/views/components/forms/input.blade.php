@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'help' => null,
    'rows' => 4,
    'autocomplete' => null,
])

@php
  $fieldId = $attributes->get('id') ?? str_replace(['[', ']', '.'], '_', $name);
  $resolvedValue = $value ?? old($name);
  $controlClass = 'form-control' . ($errors->has($name) ? ' is-invalid' : '');
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

  @if ($type === 'textarea')
    <textarea
      name="{{ $name }}"
      id="{{ $fieldId }}"
      rows="{{ $rows }}"
      {{ $attributes->except(['id', 'name', 'type', 'value', 'rows'])->merge(['class' => $controlClass]) }}
      @if ($required) required @endif
      @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    >{{ $resolvedValue }}</textarea>
  @else
    <input
      type="{{ $type }}"
      name="{{ $name }}"
      id="{{ $fieldId }}"
      value="{{ $resolvedValue }}"
      {{ $attributes->except(['id', 'name', 'type', 'value'])->merge(['class' => $controlClass]) }}
      @if ($required) required @endif
      @if ($placeholder) placeholder="{{ $placeholder }}" @endif
      @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    />
  @endif

  @isset($help)
    <div class="form-text">{{ $help }}</div>
  @endisset

  @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>
