@extends('layout.dashboard')

@section('title', 'Sub-sub-categories')

@section('content_header')
  <x-page-heading
    title="Sub-sub-categories"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-sub-categories'],
    ]"
    :add-route="auth()->user()?->is_admin ? route('sub_sub_categories.create') : null"
    add-label="Add sub-sub-category"
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
            ['key' => 'subCategory.category.name', 'label' => 'Category'],
            ['key' => 'subCategory.name', 'label' => 'Sub-category'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'slug', 'label' => 'Slug'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created'],
        ]"
        :rows="$subSubCategories"
        resource="sub_sub_categories"
      />
    </div>
    <x-pagination-footer :paginator="$subSubCategories" />
  </div>
@endsection
