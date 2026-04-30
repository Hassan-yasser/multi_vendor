@props([
  'categoryOptions',
  'subCategoryOptions',
  'subSubCategoryOptions',
  'product' => null,
  'tagOptions' => [],
])

@php
  $p = $product;
@endphp

<x-forms.input name="name" label="Name" :required="true" :value="old('name', $p?->name)" />
<x-forms.input
  name="slug"
  label="Slug"
  :value="old('slug', $p?->slug)"
  :help="'Leave empty to auto-generate from the name.'"
/>
<div class="mb-3">
  <label class="form-label">Tags</label>
  <div class="input-group">
    <select id="tag-select" class="form-select" style="flex: 1;">
      <option value="">-- Select or type a tag --</option>
      @foreach($tagOptions as $tag)
        <option value="{{ $tag }}">{{ $tag }}</option>
      @endforeach
    </select>
    <input type="text" id="tag-input" class="form-control" placeholder="Enter new tag" style="flex: 1;">
    <button type="button" id="add-tag-btn" class="btn btn-outline-secondary">Add Tag</button>
  </div>
  <div id="tags-container" class="d-flex flex-wrap gap-2 mt-2">
    @if($p)
      @foreach($p->tags as $tag)
        <span class="badge bg-primary">
          {{ $tag->name }}
          <input type="hidden" name="tags[]" value="{{ $tag->name }}">
          <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.5rem;" onclick="removeTag(this)"></button>
        </span>
      @endforeach
    @endif
  </div>
</div>
<x-forms.rich-text
  name="description"
  label="Description"
  :height="260"
  :value="old('description', $p?->description)"
/>
<x-forms.file
  name="image"
  label="Image"
  :current-path="$p?->image"
  :help="'Optional. PNG, JPG, WebP or GIF — max 4 MB.'"
/>

<x-forms.select
  name="category_id"
  label="Category"
  :options="$categoryOptions"
  :required="true"
  :selected="old('category_id', $p?->category_id)"
/>
<x-forms.select
  name="sub_category_id"
  label="Sub-category"
  :options="array_merge([['value' => '', 'label' => '— None —']], $subCategoryOptions)"
  :selected="old('sub_category_id', $p?->sub_category_id)"
/>
<x-forms.select
  name="sub_sub_category_id"
  label="Sub-sub-category"
  :options="array_merge([['value' => '', 'label' => '— None —']], $subSubCategoryOptions)"
  :selected="old('sub_sub_category_id', $p?->sub_sub_category_id)"
/>

<x-forms.input name="price" type="number" step="0.01" label="Price" :required="true" :value="old('price', $p?->price)" />

<script>
  function removeTag(btn) {
    btn.parentElement.remove();
  }

  document.addEventListener('DOMContentLoaded', function() {
    const tagSelect = document.getElementById('tag-select');
    const tagInput = document.getElementById('tag-input');
    const addTagBtn = document.getElementById('add-tag-btn');
    const tagsContainer = document.getElementById('tags-container');

    function addTag(tagName) {
      tagName = tagName.trim();
      if (!tagName) return;

      // Check if tag already exists
      const existing = tagsContainer.querySelectorAll('input[value="' + tagName + '"]').length > 0;
      if (existing) {
        alert('Tag already added!');
        return;
      }

      const tagSpan = document.createElement('span');
      tagSpan.className = 'badge bg-primary';
      tagSpan.innerHTML = tagName + '<input type="hidden" name="tags[]" value="' + tagName + '"><button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.5rem;" onclick="removeTag(this)"></button>';
      tagsContainer.appendChild(tagSpan);

      tagSelect.value = '';
      tagInput.value = '';
    }

    addTagBtn.addEventListener('click', function() {
      const tagName = tagSelect.value || tagInput.value;
      addTag(tagName);
    });

    tagInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        addTag(tagInput.value);
      }
    });
  });
</script>

