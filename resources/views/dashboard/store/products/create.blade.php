@extends('layout.dashboard')

@section('title', 'New product')

@section('content_header')
  <x-page-heading
    title="New product"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('products.index')],
        ['label' => 'Create'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('products.store') }}" class="card-body" enctype="multipart/form-data">
      @csrf
      @include('dashboard.store.products.partials.form', [
        'statusOptions' => $statusOptions,
        'categoryOptions' => $categoryOptions,
        'subCategoryOptions' => $subCategoryOptions,
        'subSubCategoryOptions' => $subSubCategoryOptions,
      ])
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
