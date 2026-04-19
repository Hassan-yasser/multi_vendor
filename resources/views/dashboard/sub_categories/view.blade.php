@extends('layout.dashboard')

@section('title', $subCategory->name)

@section('content_header')
  <x-page-heading
    :title="$subCategory->name"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-categories', 'url' => route('sub_categories.index')],
        ['label' => $subCategory->name],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <div class="p-3 d-flex align-items-center bg-body-secondary justify-content-between flex-wrap gap-2">
      <span class="text-body-secondary">Details</span>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('sub_categories.edit', $subCategory) }}" class="btn btn-sm btn-primary">Edit</a>
        <a href="{{ route('sub_categories.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Category</dt>
        <dd class="col-sm-9">{{ $subCategory->category?->name ?? '—' }}</dd>
        <dt class="col-sm-3">Name</dt>
        <dd class="col-sm-9">{{ $subCategory->name }}</dd>
        <dt class="col-sm-3">Slug</dt>
        <dd class="col-sm-9">{{ $subCategory->slug }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ $subCategory->status }}</dd>
        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">
          @if ($subCategory->description)
            <div class="border rounded p-3 bg-body-secondary catalog-html-content">{!! $subCategory->description !!}</div>
          @else
            —
          @endif
        </dd>
      </dl>
      @if ($subCategory->subSubCategories->isNotEmpty())
        <hr />
        <h5 class="mb-3">Sub-sub-categories</h5>
        <ul class="list-group">
          @foreach ($subCategory->subSubCategories as $item)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>{{ $item->name }}</span>
              <a href="{{ route('sub_sub_categories.show', $item) }}" class="btn btn-sm btn-outline-secondary">View</a>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
@endsection
