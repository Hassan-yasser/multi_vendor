@extends('layout.dashboard')

@section('title', 'Create category')

@section('content_header')
  <x-page-heading
    title="New category"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Categories', 'url' => route('categories.index')],
        ['label' => 'Create'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('categories.store') }}" class="card-body" enctype="multipart/form-data">
      @csrf
      <x-forms.input name="name" label="Name" :required="true" />
      @include('dashboard.partials.shared_catalog_fields', ['statusOptions' => $statusOptions])
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
@endsection
