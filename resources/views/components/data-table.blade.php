@props([
    'columns' => [],
    'rows' => [],
    'resource' => null,
    'showActions' => true,
    'emptyMessage' => 'No data found.',
])

@php
  $colCount = count($columns);
  $actionsCol = $showActions && $resource;
@endphp

<div {{ $attributes->merge(['class' => 'table-responsive']) }}>
  <table class="table table-striped table-hover align-middle mb-0">
    <thead class="table-light">
      <tr>
        @foreach ($columns as $col)
          <th scope="col">{{ $col['label'] }}</th>
        @endforeach
        @if ($actionsCol)
          <th scope="col" class="text-end text-nowrap" style="width: 1%">{{ __('Actions') }}</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $row)
        <tr>
          @foreach ($columns as $col)
            @php
              $key = $col['key'];
              $raw = is_array($row) ? ($row[$key] ?? null) : data_get($row, $key);
              $cell = $raw instanceof \DateTimeInterface ? $raw->format('Y-m-d H:i') : $raw;
            @endphp
            <td>
              @if ($col['key'] === 'image')

                <img src="{{ asset('storage/' . $cell) }}" alt="{{ $row->name }}" class="img-fluid" style="max-width: 100px;">
              @else
                {{ $cell }}
              @endif
            </td>
          @endforeach
          @if ($actionsCol)
            <td class="text-end text-nowrap">
              <a
                href="{{ route($resource . '.show', $row) }}"
                class="btn btn-sm btn-outline-secondary"
                title="{{ __('عرض') }}"
              >
                <i class="bi bi-eye"></i>
              </a>
              <a
                href="{{ route($resource . '.edit', $row) }}"
                class="btn btn-sm btn-outline-primary"
                title="{{ __('Edit') }}"
              >
                <i class="bi bi-pencil"></i>
              </a>
              <form
                action="{{ route($resource . '.destroy', $row) }}"
                method="post"
                class="d-inline"
                onsubmit="return confirm(@json(__('Are you sure you want to delete this item?')));"
              >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          @endif
        </tr>
      @empty
        <tr>
          <td colspan="{{ $colCount + ($actionsCol ? 1 : 0) }}" class="text-center text-secondary py-4">
            {{ $emptyMessage }}
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
