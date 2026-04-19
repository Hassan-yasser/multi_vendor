@extends('layout.dashboard')

@section('title', 'Edit sub-sub-category')

@section('content_header')
  <x-page-heading
    title="Edit sub-sub-category"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sub-sub-categories', 'url' => route('sub_sub_categories.index')],
        ['label' => 'Edit'],
    ]"
  />
@endsection

@section('content')
  <div class="card shadow-sm">
    <form method="post" action="{{ route('sub_sub_categories.update', $subSubCategory) }}" class="card-body" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <x-forms.select
        name="sub_category_id"
        label="Sub-category"
        :options="$subCategoryOptions"
        :required="true"
        :selected="old('sub_category_id', $subSubCategory->sub_category_id)"
      />
      <x-forms.input name="name" label="Name" :required="true" :value="old('name', $subSubCategory->name)" />
      <x-forms.input name="slug" label="Slug" :value="old('slug', $subSubCategory->slug)" />
      <x-forms.rich-text
        name="description"
        label="Description"
        :value="old('description', $subSubCategory->description)"
        :height="260"
      />
      <x-forms.file
        name="image"
        label="Image"
        :currentPath="$subSubCategory->image"
        :help="'Leave empty to keep the current image. PNG, JPG, WebP or GIF — max 4 MB.'"
      />
      <x-forms.select
        name="status"
        label="Status"
        :options="$statusOptions"
        :required="true"
        :selected="old('status', $subSubCategory->status)"
      />
      <x-forms.input name="meta_title" label="Meta title" :value="old('meta_title', $subSubCategory->meta_title)" />
      <x-forms.input
        name="meta_description"
        type="textarea"
        label="Meta description"
        :rows="3"
        :value="old('meta_description', $subSubCategory->meta_description)"
      />
      <x-forms.input
        name="meta_keywords"
        label="Meta keywords"
        :value="old('meta_keywords', $subSubCategory->meta_keywords)"
      />
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('sub_sub_categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
@endsection
