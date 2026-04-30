@extends('layout.dashboard')

@section('title', 'Edit product')

@section('content_header')
  <x-page-heading
    title="Edit product"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Products', 'url' => route('products.index')],
        ['label' => $product->name],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('products.update', $product) }}" class="card-body" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('dashboard.store.products.partials.form', [
        'statusOptions' => $statusOptions,
        'categoryOptions' => $categoryOptions,
        'subCategoryOptions' => $subCategoryOptions,
        'subSubCategoryOptions' => $subSubCategoryOptions,
        'product' => $product,
      ])
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
