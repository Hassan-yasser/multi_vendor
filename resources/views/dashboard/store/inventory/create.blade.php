@extends('layout.dashboard')
@section('title','Add Inventory')
@section('content_header')
<x-page-heading title="Add Inventory" :breadcrumb-items="[['label'=>'Dashboard','url'=>route('dashboard')],['label'=>'Inventory','url'=>route('inventory.index')],['label'=>'Add']]"/>
@endsection
@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{route('inventory.store')}}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Inventory Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" required>
          @error('name')<div class="invalid-feedback">{{$message}}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{old('address')}}">
          @error('address')<div class="invalid-feedback">{{$message}}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Select Products</label>
          <div class="row g-3" id="productsGrid">
            @forelse($products as $p)
            @php
              $activeDiscount = $p->active_discount;
              $discountPercent = $activeDiscount ? round((($p->price - $activeDiscount->discount_price) / $p->price) * 100) : 0;
            @endphp
            <div class="col-md-6 col-lg-4">
              <div class="card product-card cursor-pointer h-100" onclick="toggleProductCard(this,{{$p->id}})">
                <div class="card-body p-3">
                  <input type="checkbox" name="products[]" value="{{$p->id}}" class="form-check-input position-absolute top-0 end-0 m-2 product-check" style="z-index:10">
                  <div class="d-flex gap-3">
                    @if($p->image)
                    <img src="{{asset('storage/'.$p->image)}}" class="rounded flex-shrink-0" style="width:80px;height:80px;object-fit:cover">
                    @else
                    <div class="bg-light rounded flex-shrink-0 d-flex align-items-center justify-content-center" style="width:80px;height:80px"><i class="bi bi-image text-muted fs-4"></i></div>
                    @endif
                    <div class="flex-grow-1 min-width-0">
                      <h6 class="fw-semibold mb-1 text-truncate">{{$p->name}}</h6>
                      <div class="small text-muted mb-1">{{$p->category?->name ?? 'No Category'}}</div>
                      <div class="mb-1">
                        @foreach($p->tags as $tag)
                        <span class="badge bg-secondary me-1">{{$tag->name}}</span>
                        @endforeach
                      </div>
                      <div class="d-flex align-items-center flex-wrap gap-1">
                        @if($activeDiscount)
                        <span class="text-decoration-line-through text-muted small">${{number_format($p->price,2)}}</span>
                        <span class="fw-bold text-success">${{number_format($activeDiscount->discount_price,2)}}</span>
                        <span class="badge bg-danger">-{{$discountPercent}}%</span>
                        @else
                        <span class="fw-bold">${{number_format($p->price,2)}}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                  <input type="number" name="quantities[{{$p->id}}]" class="form-control form-control-sm product-qty mt-2" value="0" min="0" placeholder="Quantity" style="display:none">
                </div>
              </div>
            </div>
            @empty
            <div class="col-12"><div class="alert alert-info">No products available. Add products first.</div></div>
            @endforelse
          </div>
        </div>
        <div class="col-12 mt-4">
          <button type="submit" class="btn btn-primary">Save</button>
          <a href="{{route('inventory.index')}}" class="btn btn-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<style>.product-card{cursor:pointer;transition:all .2s}.product-card:hover{transform:translateY(-2px);box-shadow:0 .5rem 1rem rgba(0,0,0,.15)}.product-card.selected{border:2px solid #0d6efd;background:#f8f9ff}.product-card .product-qty{display:none}.product-card.selected .product-qty{display:block}</style>
<script>
function toggleProductCard(card,id){
  const check=card.querySelector('.product-check'),qty=card.querySelector('.product-qty');
  check.checked=!check.checked;card.classList.toggle('selected',check.checked);qty.style.display=check.checked?'block':'none';if(check.checked){qty.focus();qty.select();}
}
</script>
@endsection
