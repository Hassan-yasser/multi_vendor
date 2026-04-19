@props([
    'paginator' => null,
])

@if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages())
  <div {{ $attributes->merge(['class' => 'card-footer d-flex justify-content-end']) }}>
    {{ $paginator->withQueryString()->links() }}
  </div>
@elseif ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->total() > 0)
  <div {{ $attributes->merge(['class' => 'card-footer text-body-secondary small']) }}>
    عرض {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} من {{ $paginator->total() }}
  </div>
@endif
