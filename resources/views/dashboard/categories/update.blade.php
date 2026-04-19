@extends('layout.dashboard')

@section('title', 'Edit category')

@section('content_header')
  <x-page-heading
    title="Edit category"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Categories', 'url' => route('categories.index')],
        ['label' => 'Edit'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('categories.update', $category) }}" class="card-body" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <x-forms.input name="name" label="Name" :required="true" :value="old('name', $category->name)" />
      <x-forms.input
        name="slug"
        label="Slug"
        :value="old('slug', $category->slug)"
        :help="'Leave empty to auto-generate from the name.'"
      />
      <x-forms.rich-text
        name="description"
        label="Description"
        :value="old('description', $category->description)"
        :height="260"
      />
      <x-forms.file
        name="image"
        label="Image"
        :currentPath="$category->image"
        :help="'Leave empty to keep the current image. PNG, JPG, WebP or GIF — max 4 MB.'"
      />
      <x-forms.select
        name="status"
        label="Status"
        :options="$statusOptions"
        :required="true"
        :selected="old('status', $category->status)"
      />
      <x-forms.input name="meta_title" label="Meta title" :value="old('meta_title', $category->meta_title)" />
      <x-forms.input
        name="meta_description"
        type="textarea"
        label="Meta description"
        :rows="3"
        :value="old('meta_description', $category->meta_description)"
      />
      <x-forms.input
        name="meta_keywords"
        label="Meta keywords"
        :value="old('meta_keywords', $category->meta_keywords)"
      />
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
@endsection
