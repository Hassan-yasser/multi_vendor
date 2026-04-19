@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'height' => 240,
    'help' => null,
])

@php
  $fieldId = $attributes->get('id') ?? str_replace(['[', ']', '.'], '_', $name);
  $resolved = old($name, $value ?? '');
@endphp

@once('summernote-assets')
  @push('styles')
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css"
      crossorigin="anonymous"
    />
  @endpush
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"
      crossorigin="anonymous"
    ></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.summernote) {
          return;
        }
        document.querySelectorAll('[data-summernote-editor]').forEach(function (el) {
          if (el.dataset.summernoteReady === '1') {
            return;
          }
          el.dataset.summernoteReady = '1';
          var h = parseInt(el.getAttribute('data-height') || '240', 10);
          window.jQuery(el).summernote({
            height: h,
            dialogsInBody: true,
            toolbar: [
              ['style', ['style']],
              ['font', ['bold', 'italic', 'underline', 'clear']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['insert', ['link']],
              ['view', ['fullscreen', 'codeview', 'help']],
            ],
          });
        });
      });
    </script>
  @endpush
@endonce

<div class="mb-3">
  @isset($label)
    <label for="{{ $fieldId }}" class="form-label">
      {{ $label }}
      @if ($required)
        <span class="text-danger" aria-hidden="true">*</span>
      @endif
    </label>
  @endisset

  <textarea
    name="{{ $name }}"
    id="{{ $fieldId }}"
    data-summernote-editor
    data-height="{{ $height }}"
    {{ $attributes->except(['id', 'name'])->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    @if ($required)
      required
    @endif
  >{!! $resolved !!}</textarea>

  @isset($help)
    <div class="form-text">{{ $help }}</div>
  @endisset

  @error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>
