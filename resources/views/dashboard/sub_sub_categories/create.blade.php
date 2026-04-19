@extends('layout.dashboard')

@section('title', 'Create sub-sub-category')

@section('content_header')
  <x-page-heading
    title="New sub-sub-category"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-sub-categories', 'url' => route('sub_sub_categories.index')],
        ['label' => 'Create'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('sub_sub_categories.store') }}" class="card-body" enctype="multipart/form-data">
      @csrf
      <x-forms.select
        name="sub_category_id"
        label="Sub-category"
        :options="$subCategoryOptions"
        :required="true"
      />
      <x-forms.input name="name" label="Name" :required="true" />
      @include('dashboard.catalog.partials.shared_catalog_fields', ['statusOptions' => $statusOptions])
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('sub_sub_categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
@endsection
