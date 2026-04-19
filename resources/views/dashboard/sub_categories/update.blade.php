@extends('layout.dashboard')

@section('title', 'Edit sub-category')

@section('content_header')
  <x-page-heading
    title="Edit sub-category"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-categories', 'url' => route('sub_categories.index')],
        ['label' => 'Edit'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('sub_categories.update', $subCategory) }}" class="card-body" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <x-forms.select
        name="category_id"
        label="Category"
        :options="$categoryOptions"
        :required="true"
        :selected="old('category_id', $subCategory->category_id)"
      />
      <x-forms.input name="name" label="Name" :required="true" :value="old('name', $subCategory->name)" />
      <x-forms.input name="slug" label="Slug" :value="old('slug', $subCategory->slug)" />
      <x-forms.rich-text
        name="description"
        label="Description"
        :value="old('description', $subCategory->description)"
        :height="260"
      />
      <x-forms.file
        name="image"
        label="Image"
        :currentPath="$subCategory->image"
        :help="'Leave empty to keep the current image. PNG, JPG, WebP or GIF — max 4 MB.'"
      />
      <x-forms.select
        name="status"
        label="Status"
        :options="$statusOptions"
        :required="true"
        :selected="old('status', $subCategory->status)"
      />
      <x-forms.input name="meta_title" label="Meta title" :value="old('meta_title', $subCategory->meta_title)" />
      <x-forms.input
        name="meta_description"
        type="textarea"
        label="Meta description"
        :rows="3"
        :value="old('meta_description', $subCategory->meta_description)"
      />
      <x-forms.input
        name="meta_keywords"
        label="Meta keywords"
        :value="old('meta_keywords', $subCategory->meta_keywords)"
      />
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('sub_categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
@endsection
