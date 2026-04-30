@extends('layout.dashboard')

@section('title', 'Stores')

@section('content_header')
  <x-page-heading
    title="Stores"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Stores'],
    ]"
  />
@endsection

@section('page_title', 'Stores')

@section('content')
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>{{ __('Logo') }}</th>
              <th>{{ __('Cover') }}</th>
              <th>{{ __('Name') }}</th>
              <th>{{ __('Products') }}</th>
              <th>{{ __('Orders') }}</th>
              <th class="text-end">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($stores as $store)
              <tr>
                <td>{{ $store->id }}</td>
                <td>
                  @if ($store->logo)
                    <img src="{{ asset('storage/'.$store->logo) }}" alt="" class="rounded border" style="max-width: 56px; max-height: 56px; object-fit: contain" />
                  @else
                    —
                  @endif
                </td>
                <td>
                  @if ($store->cover_image)
                    <img src="{{ asset('storage/'.$store->cover_image) }}" alt="" class="rounded border" style="max-width: 56px; max-height: 56px; object-fit: contain" />
                  @else
                    —
                  @endif
                </td>
                <td>{{ $store->name }}</td>
                <td>{{ $store->products_count }}</td>
                <td>{{ $store->orders_count }}</td>
                <td class="text-end">
                  <a href="{{ route('admin.stores.show', $store) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-graph-up-arrow"></i> {{ __('Stats') }}
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-secondary py-4">{{ __('No stores.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <x-pagination-footer :paginator="$stores" />
  </div>
@endsection
