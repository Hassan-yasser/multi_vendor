@extends('layout.dashboard')

@section('title', 'Sub-categories')

@section('content_header')
  <x-page-heading
    title="Sub-categories"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-categories'],
    ]"
    :add-route="auth()->user()?->is_admin ? route('sub_categories.create') : null"
    add-label="Add sub-category"
  />
@endsection

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <x-data-table
        :columns="[
            ['key' => 'id', 'label' => '#'],
            ['key' => 'image', 'label' => 'Image'],
            ['key' => 'category.name', 'label' => 'Category'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'slug', 'label' => 'Slug'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created'],
        ]"
        :rows="$subCategories"
        resource="sub_categories"
      />
    </div>
    <x-pagination-footer :paginator="$subCategories" />
  </div>
@endsection
