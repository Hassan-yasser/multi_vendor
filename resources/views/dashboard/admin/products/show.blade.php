@extends('layout.dashboard')

@section('title', $product->name)

@section('content_header')
  <x-page-heading
    :title="$product->name"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('admin.products.index')],
        ['label' => $product->name],
    ]"
  />
@endsection

@section('content')
  <div class="alert alert-info py-2 small">{{ __('Admin view only — editing is done by the store owner.') }}</div>

  <div class="card shadow-sm">
    <div class="p-3 d-flex align-items-center bg-body-secondary justify-content-between flex-wrap gap-2">
      <span class="text-body-secondary">{{ __('Product') }}</span>
      <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back') }}</a>
    </div>
    <div class="card-body">
      @if ($product->image)
        <div class="mb-4 text-center border-bottom pb-3">
          <img
            src="{{ asset('storage/'.$product->image) }}"
            alt=""
            class="img-fluid rounded border"
            style="max-height: 280px"
          />
        </div>
      @endif
      <dl class="row mb-0">
        <dt class="col-sm-3">{{ __('Store') }}</dt>
        <dd class="col-sm-9">{{ $product->store?->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('Category') }}</dt>
        <dd class="col-sm-9">{{ $product->category?->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('Name') }}</dt>
        <dd class="col-sm-9">{{ $product->name }}</dd>
        <dt class="col-sm-3">{{ __('Price') }}</dt>
        <dd class="col-sm-9">{{ number_format($product->price, 2) }}</dd>
        <dt class="col-sm-3">{{ __('Tags') }}</dt>
        <dd class="col-sm-9">{{ $product->tag_list ?: '—' }}</dd>
        <dt class="col-sm-3">{{ __('Status') }}</dt>
        <dd class="col-sm-9">{{ $product->status }}</dd>
        <dt class="col-sm-3">{{ __('Description') }}</dt>
        <dd class="col-sm-9">
          @if ($product->description)
            <div class="border rounded p-3 bg-body-secondary catalog-html-content">{!! $product->description !!}</div>
          @else
            —
          @endif
        </dd>
      </dl>
    </div>
  </div>
@endsection
