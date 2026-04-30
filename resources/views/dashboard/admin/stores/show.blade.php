@extends('layout.dashboard')

@section('title', $store->name)

@section('content_header')
  <x-page-heading
    :title="$store->name"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Stores', 'url' => route('admin.stores.index')],
        ['label' => $store->name],
    ]"
  />
@endsection

@section('content')
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-body-secondary small">{{ __('Orders') }}</div>
          <div class="fs-4 fw-semibold">{{ number_format($stats['orders_count']) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-body-secondary small">{{ __('Revenue') }}</div>
          <div class="fs-4 fw-semibold">{{ number_format($stats['revenue'], 2) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-body-secondary small">{{ __('Products') }}</div>
          <div class="fs-4 fw-semibold">{{ number_format($stats['products_count']) }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-body-secondary small">{{ __('Avg. rating') }}</div>
          <div class="fs-4 fw-semibold">{{ $stats['avg_rating'] }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header">{{ __('Store profile') }}</div>
    <div class="card-body row g-3">
      <div class="col-md-6">
        @if ($store->logo)
          <div class="small text-body-secondary mb-1">{{ __('Logo') }}</div>
          <img src="{{ asset('storage/'.$store->logo) }}" alt="" class="img-fluid rounded border" style="max-height: 100px" />
        @endif
      </div>
      <div class="col-md-6">
        @if ($store->cover_image)
          <div class="small text-body-secondary mb-1">{{ __('Cover') }}</div>
          <img src="{{ asset('storage/'.$store->cover_image) }}" alt="" class="img-fluid rounded border" style="max-height: 100px" />
        @endif
      </div>
      <div class="col-12">
        <strong>{{ __('Slug') }}:</strong> {{ $store->slug }}
      </div>
      @if ($store->description)
        <div class="col-12">
          {{ $store->description }}
        </div>
      @endif
    </div>
  </div>

  <div class="d-flex gap-2">
    <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary">{{ __('Back to stores') }}</a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">{{ __('Browse all products') }}</a>
  </div>
@endsection
