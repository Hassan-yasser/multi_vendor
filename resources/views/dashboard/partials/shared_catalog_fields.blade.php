@props([
    'statusOptions',
])

<x-forms.input
  name="slug"
  label="Slug"
  :help="'Leave empty to auto-generate from the name.'"
/>
<x-forms.rich-text name="description" label="Description" :height="260" />
<x-forms.file name="image" label="Image" :help="'Optional. PNG, JPG, WebP or GIF — max 4 MB (must fit under PHP upload_max_filesize).'" />
<x-forms.select name="status" label="Status" :options="$statusOptions" :required="true" />
<x-forms.input name="meta_title" label="Meta title" />
<x-forms.input name="meta_description" type="textarea" label="Meta description" :rows="3" />
<x-forms.input name="meta_keywords" label="Meta keywords" />
