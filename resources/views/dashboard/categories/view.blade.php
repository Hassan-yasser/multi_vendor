@extends('layout.dashboard')

@section('title', $category->name)

@section('content_header')
  <x-page-heading
    :title="$category->name"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Categories', 'url' => route('categories.index')],
        ['label' => $category->name],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <div class="p-3 d-flex align-items-center bg-body-secondary justify-content-between flex-wrap gap-2">
      <span class="text-body-secondary">Details</span>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">Edit</a>
        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
      </div> 
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">ID</dt>
        <dd class="col-sm-9">{{ $category->id }}</dd>
        <dt class="col-sm-3">Name</dt>
        <dd class="col-sm-9">{{ $category->name }}</dd>
        <dt class="col-sm-3">Slug</dt>
        <dd class="col-sm-9">{{ $category->slug }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ $category->status }}</dd>
        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">
          @if ($category->description)
            <div class="border rounded p-3 bg-body-secondary catalog-html-content">{!! $category->description !!}</div>
          @else
            —
          @endif
        </dd>
        <dt class="col-sm-3">Created</dt>
        <dd class="col-sm-9">{{ $category->created_at }}</dd>
      </dl>
      @if ($category->subCategories->isNotEmpty())
        <hr />
        <h5 class="mb-3">Sub-categories</h5>
        <ul class="list-group">
          @foreach ($category->subCategories as $sub)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>{{ $sub->name }}</span>
              <a href="{{ route('sub_categories.show', $sub) }}" class="btn btn-sm btn-outline-secondary">View</a>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
@endsection
