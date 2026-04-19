@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'help' => null,
])

@php
  $fieldId = $attributes->get('id') ?? str_replace(['[', ']', '.'], '_', $name);
  $resolvedSelected = $selected ?? old($name);
  $controlClass = 'form-select' . ($errors->has($name) ? ' is-invalid' : '');
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

  <select
    name="{{ $name }}"
    id="{{ $fieldId }}"
    {{ $attributes->except(['id', 'name'])->merge(['class' => $controlClass]) }}
    @if ($required) required @endif
  >
    @foreach ($options as $opt)
      @php
        $optValue = (string) ($opt['value'] ?? '');
        $optLabel = (string) ($opt['label'] ?? '');
        $isSelected = (string) ($resolvedSelected ?? '') === $optValue;
      @endphp
      <option value="{{ $optValue }}" @selected($isSelected)>{{ $optLabel }}</option>
    @endforeach
  </select>

  @isset($help)
    <div class="form-text">{{ $help }}</div>
  @endisset

  @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>
