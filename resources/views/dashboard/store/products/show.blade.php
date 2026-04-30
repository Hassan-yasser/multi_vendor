@extends('layout.dashboard')

@section('title', $product->name)

@section('content_header')
  <x-page-heading
    :title="$product->name"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('products.index')],
        ['label' => $product->name],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <div class="p-3 d-flex align-items-center bg-body-secondary justify-content-between flex-wrap gap-2">
      <span class="text-body-secondary">{{ __('Details') }}</span>
      <div class="d-flex gap-2">
        @can('update', $product)
          <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
        @endcan
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Back') }}</a>
      </div>
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
        <dt class="col-sm-3">ID</dt>
        <dd class="col-sm-9">{{ $product->id }}</dd>
        <dt class="col-sm-3">{{ __('Store') }}</dt>
        <dd class="col-sm-9">{{ $product->store?->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('Category') }}</dt>
        <dd class="col-sm-9">{{ $product->category?->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('Sub-category') }}</dt>
        <dd class="col-sm-9">{{ $product->subCategory?->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('Sub-sub-category') }}</dt>
        <dd class="col-sm-9">{{ $product->subSubCategory?->name ?? '—' }}</dd>
        <dt class="col-sm-3">{{ __('Slug') }}</dt>
        <dd class="col-sm-9">{{ $product->slug }}</dd>
        <dt class="col-sm-3">{{ __('Status') }}</dt>
        <dd class="col-sm-9">{{ $product->status }}</dd>
        <dt class="col-sm-3">{{ __('Tags') }}</dt>
        <dd class="col-sm-9">{{ $product->tag_list ?: '—' }}</dd>
        <dt class="col-sm-3">{{ __('Price') }}</dt>
        <dd class="col-sm-9">{{ number_format($product->price, 2) }}</dd>
        <dt class="col-sm-3">{{ __('Discount price') }}</dt>
        <dd class="col-sm-9">{{ number_format($product->discount_price, 2) }}</dd>
        <dt class="col-sm-3">{{ __('Rating') }}</dt>
        <dd class="col-sm-9">{{ $product->rating }}</dd>
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
