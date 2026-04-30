@props([
    'columns' => [],
    'rows' => [],
    'resource' => null,
    'showActions' => true,
    'emptyMessage' => 'No data found.',
    /** If null, only users with is_admin see edit/delete; view remains for everyone. */
    'manageActions' => null,
])

@php
  $colCount = count($columns);
  $actionsCol = $showActions && $resource;
  $canManage = $manageActions ?? (bool) (auth()->user()?->is_admin);
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
              @if (in_array($col['key'], ['image', 'logo', 'cover_image'], true))
                @if ($cell)
                  <img
                    src="{{ asset('storage/'.$cell) }}"
                    alt=""
                    class="img-fluid rounded border"
                    style="max-width: 72px; max-height: 72px; object-fit: contain"
                  />
                @else
                  —
                @endif
              @elseif ($col['key'] === 'price')
                {{ $cell !== null && $cell !== '' ? number_format((float) $cell, 2) : '—' }}
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
                title="{{ __('View') }}"
              >
                <i class="bi bi-eye"></i>
              </a>
              @if ($canManage)
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
              @endif
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
