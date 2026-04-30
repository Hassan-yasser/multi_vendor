@extends('layout.dashboard')

@section('title', 'Stores')

@section('content_header')
  <x-page-heading
    title="Stores"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Stores'],
    ]"
  />
@endsection

@section('page_title', 'Stores')

@section('content')
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <x-data-table
        :columns="[
            ['key' => 'id', 'label' => '#'],
            ['key' => 'logo', 'label' => 'Logo'],
            ['key' => 'cover_image', 'label' => 'Cover'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'slug', 'label' => 'Slug'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created'],
        ]"
        :rows="$stores"
        :show-actions="false"
        empty-message="No stores yet."
      />
    </div>
    <x-pagination-footer :paginator="$stores" />
  </div>
@endsection
