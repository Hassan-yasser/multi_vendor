@extends('layout.dashboard')

@section('title', 'Products')

@section('content_header')
  <x-page-heading
    title="Products"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Products'],
    ]"
  />
@endsection

@section('page_title', 'Products')

@section('content')
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <x-data-table
        :columns="[
            ['key' => 'id', 'label' => '#'],
            ['key' => 'image', 'label' => 'Image'],
            ['key' => 'name', 'label' => 'Name'],
            <!-- ['key' => 'category', 'label' => 'Category'], -->
            ['key' => 'price', 'label' => 'Price'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created'],
        ]"
        :rows="$products"
        :show-actions="false"
        empty-message="No products yet."
      />
    </div>
    <x-pagination-footer :paginator="$products" />
  </div>
@endsection
