@props([
    'title',
    'breadcrumbItems' => [],
    'addRoute' => null,
    'addLabel' => 'Add',
])

<div class="d-flex align-items-center flex-wrap gap-2 gap-md-3">
  
  <h3 class="mb-0 flex-shrink-0">{{ $title }}</h3>
  
  @if (count($breadcrumbItems))
  <x-breadcrumb :items="$breadcrumbItems" class="breadcrumb mb-0 flex-grow-1" />
  @endif
  @isset($addRoute)
    <a href="{{ $addRoute }}" class="btn btn-primary btn-sm flex-shrink-0">
      <i class="bi bi-plus-lg me-1"></i>
      {{ $addLabel }}
    </a>
  @endisset
</div>
