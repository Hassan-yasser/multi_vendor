@extends('layout.dashboard')

@section('title', $subSubCategory->name)

@section('content_header')
  <x-page-heading
    :title="$subSubCategory->name"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-sub-categories', 'url' => route('sub_sub_categories.index')],
        ['label' => $subSubCategory->name],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <div class="p-3 d-flex align-items-center bg-body-secondary justify-content-between flex-wrap gap-2">
      <span class="text-body-secondary">Details</span>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('sub_sub_categories.edit', $subSubCategory) }}" class="btn btn-sm btn-primary">Edit</a>
        <a href="{{ route('sub_sub_categories.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Category</dt>
        <dd class="col-sm-9">{{ $subSubCategory->subCategory?->category?->name ?? '—' }}</dd>
        <dt class="col-sm-3">Sub-category</dt>
        <dd class="col-sm-9">{{ $subSubCategory->subCategory?->name ?? '—' }}</dd>
        <dt class="col-sm-3">Name</dt>
        <dd class="col-sm-9">{{ $subSubCategory->name }}</dd>
        <dt class="col-sm-3">Slug</dt>
        <dd class="col-sm-9">{{ $subSubCategory->slug }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ $subSubCategory->status }}</dd>
        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">
          @if ($subSubCategory->description)
            <div class="border rounded p-3 bg-body-secondary catalog-html-content">{!! $subSubCategory->description !!}</div>
          @else
            —
          @endif
        </dd>
      </dl>
    </div>
  </div>
@endsection
