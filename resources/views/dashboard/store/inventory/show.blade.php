@extends('layout.dashboard')
@section('title',$inventory->name)
@section('content_header')
<x-page-heading title="{{$inventory->name}}" :breadcrumb-items="[['label'=>'Dashboard','url'=>route('dashboard')],['label'=>'Inventory','url'=>route('inventory.index')],['label'=>$inventory->name]]"/>
@endsection
@section('content')
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6"><strong>ID:</strong> {{$inventory->id}}</div>
      <div class="col-md-6"><strong>Name:</strong> {{$inventory->name}}</div>
      <div class="col-md-6"><strong>Address:</strong> {{$inventory->address??'—'}}</div>
      <div class="col-md-6"><strong>Products Count:</strong> <span class="badge bg-info">{{$products->total()}}</span></div>
    </div>
  </div>
</div>

<h5 class="mb-3">Products</h5>
<div class="row g-3">
  @forelse($products as $p)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <div class="d-flex gap-3">
          @if($p->image)<img src="{{asset('storage/'.$p->image)}}" class="rounded" style="width:90px;height:90px;object-fit:cover">@endif
          <div class="flex-grow-1">
            <h6 class="card-title mb-1">{{$p->name}}</h6>
            <p class="text-muted small mb-1">{{$p->category?->name??'No Category'}}</p>
            @if($p->tags->count())
            <p class="mb-1">
              @foreach($p->tags as $tag)
              <span class="badge bg-secondary">{{$tag->name}}</span>
              @endforeach
            </p>
            @endif
            <p class="mb-0">
              @if($p->active_discount)
              <span class="text-decoration-line-through text-muted small">${{number_format($p->price, 2)}}</span>
              <span class="fw-bold text-success">${{number_format($p->active_discount->discount_price, 2)}}</span>
              <span class="badge bg-danger ms-1">-{{round((($p->price - $p->active_discount->discount_price) / $p->price) * 100)}}%</span>
              @else
              <span class="fw-bold">${{number_format($p->price, 2)}}</span>
              @endif
            </p>
            <p class="mb-0 mt-1"><span class="badge bg-success">Qty: {{$p->pivot->quantity}}</span></p>
          </div>
        </div>
      </div>
      <div class="card-footer bg-transparent">
        <button type="button" class="btn btn-sm btn-outline-success w-100" onclick="openProductRestock({{$inventory->id}},{{$p->id}},'{{$p->name}}',{{$p->pivot->quantity}},{{json_encode($p->image?asset('storage/'.$p->image):null)}},{{json_encode($p->category?->name??'No Category')}},{{json_encode($p->tags->pluck('name')->toArray())}},{{$p->price}},{{json_encode($p->active_discount?->discount_price??null)}})"><i class="bi bi-box-arrow-in-down"></i> Restock</button>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12"><div class="alert alert-info">No products in this inventory.</div></div>
  @endforelse
</div>

<div class="mt-4">{{$products->links()}}</div>

<div class="modal fade" id="productRestockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Restock Product</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" id="restockInvId">
        <input type="hidden" id="restockProdId">
        <div class="d-flex gap-3 mb-3">
          <img id="restockProdImage" src="" class="rounded" style="width:120px;height:120px;object-fit:cover;display:none">
          <div class="flex-grow-1">
            <h5 id="restockProdName" class="mb-2"></h5>
            <p class="mb-1 text-muted" id="restockProdCategory"></p>
            <p class="mb-2" id="restockProdTags"></p>
            <p class="mb-0" id="restockProdPrice"></p>
          </div>
        </div>
        <div class="card bg-light mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>Current Quantity:</strong> <span class="badge bg-info" id="restockCurrentQty"></span></p>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">New Quantity</label>
          <input type="number" id="restockNewQty" class="form-control" min="0" placeholder="Enter new quantity">
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" onclick="saveProductRestock()">Save</button></div>
    </div>
  </div>
</div>

<script>
let productRestockModal;
document.addEventListener('DOMContentLoaded',function(){productRestockModal=new bootstrap.Modal(document.getElementById('productRestockModal'));});
function openProductRestock(invId,prodId,prodName,currentQty,image,category,tags,price,discountPrice){
  document.getElementById('restockInvId').value=invId;
  document.getElementById('restockProdId').value=prodId;
  document.getElementById('restockProdName').textContent=prodName;
  document.getElementById('restockCurrentQty').textContent=currentQty;
  document.getElementById('restockNewQty').value=currentQty;

  // Update image
  const imgEl=document.getElementById('restockProdImage');
  if(image){imgEl.src=image;imgEl.style.display='block';}else{imgEl.style.display='none';}

  // Update category
  document.getElementById('restockProdCategory').textContent=category||'No Category';

  // Update tags
  const tagsEl=document.getElementById('restockProdTags');
  if(tags&&tags.length){tagsEl.innerHTML=tags.map(t=>'<span class="badge bg-secondary me-1">'+t+'</span>').join('');tagsEl.style.display='block';}else{tagsEl.style.display='none';}

  // Update price
  const priceEl=document.getElementById('restockProdPrice');
  if(discountPrice){
    const discountPercent=Math.round(((price-discountPrice)/price)*100);
    priceEl.innerHTML='<span class="text-decoration-line-through text-muted">$'+price.toFixed(2)+'</span> <span class="fw-bold text-success">$'+discountPrice.toFixed(2)+'</span> <span class="badge bg-danger">-'+discountPercent+'%</span>';
  }else{
    priceEl.innerHTML='<span class="fw-bold">$'+price.toFixed(2)+'</span>';
  }

  productRestockModal.show();
}
function saveProductRestock(){
  const invId=document.getElementById('restockInvId').value;
  const prodId=document.getElementById('restockProdId').value;
  const qty=parseInt(document.getElementById('restockNewQty').value)||0;
  fetch(`{{route('inventory.index')}}/${invId}/restock/${prodId}`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({quantity:qty})}).then(r=>r.json()).then(d=>{if(d.success){productRestockModal.hide();location.reload();}else alert('Error: '+d.message);}).catch(e=>alert('Error'));
}
</script>
@endsection
