@extends('layout.dashboard')
@section('title','Inventory')
@section('content_header')
<x-page-heading title="Inventory" :breadcrumb-items="[['label'=>'Dashboard','url'=>route('dashboard')],['label'=>'Inventory']]" :add-route="route('inventory.create')" add-label="Add Inventory"/>
@endsection
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{session('success')}}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Name</th><th>Address</th><th>Products Count</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse($inventories as $inv)
          <tr>
            <td>{{$inv->id}}</td>
            <td class="fw-semibold">{{$inv->name}}</td>
            <td>{{$inv->address??'—'}}</td>
            <td><span class="badge bg-info">{{$inv->products_count??0}}</span></td>
            <td class="text-end text-nowrap">
              <a href="{{route('inventory.show',$inv)}}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
              <a href="{{route('inventory.edit',$inv)}}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
              <button type="button" class="btn btn-sm btn-outline-success" onclick="openRestockModal({{$inv->id}})" title="Restock"><i class="bi bi-box-arrow-in-down"></i></button>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="showDeleteModal({{$inv->id}},'{{$inv->name}}')" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          @empty<tr><td colspan="5" class="text-center text-secondary py-4">No inventories found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Restock Inventory</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" id="restockInventoryId">
        <ul class="nav nav-tabs mb-3" id="restockTab" role="tablist">
          <li class="nav-item" role="presentation"><button class="nav-link active" id="restock-tab" data-bs-toggle="tab" data-bs-target="#restock-panel" type="button">Restock Products</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="add-tab" data-bs-toggle="tab" data-bs-target="#add-panel" type="button">Add Product</button></li>
        </ul>
        <div class="tab-content" id="restockTabContent">
          <div class="tab-pane fade show active" id="restock-panel"><div id="restockProductsList" class="table-responsive"><p class="text-muted">Loading...</p></div></div>
          <div class="tab-pane fade" id="add-panel"><div id="addProductsList"><p class="text-muted">Loading...</p></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" onclick="saveRestock()">Save</button></div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Delete Inventory</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="deleteInventoryName"></strong>?</p>
        <p class="text-muted small">This action cannot be undone. All product associations will be removed.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let restockModal,deleteModal,storeProducts=[];
document.addEventListener('DOMContentLoaded',function(){restockModal=new bootstrap.Modal(document.getElementById('restockModal'));deleteModal=new bootstrap.Modal(document.getElementById('deleteModal'));fetchStoreProducts();});
function showDeleteModal(id,name){
  document.getElementById('deleteInventoryName').textContent=name;
  document.getElementById('deleteForm').action='{{url("inventory")}}/'+id;
  deleteModal.show();
}
function fetchStoreProducts(){
  fetch('{{route("inventory.index")}}',{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
    .then(r=>r.json())
    .then(d=>{
      if(d.products){
        storeProducts=d.products;
        console.log('Loaded '+storeProducts.length+' store products');
      }else{
        console.error('No products in response:',d);
        storeProducts=[];
      }
    })
    .catch(e=>{
      console.error('Error loading store products:',e);
      storeProducts=[];
    });
}
function openRestockModal(id){
  document.getElementById('restockInventoryId').value=id;
  document.getElementById('restockProductsList').innerHTML='<p class="text-muted">Loading...</p>';
  document.getElementById('addProductsList').innerHTML='<p class="text-muted">Loading...</p>';
  restockModal.show();
  fetch('{{url("inventory")}}/'+id+'/products').then(r=>r.json()).then(d=>{
    if(d.success||Array.isArray(d.products)){
      const products=d.products||[];
      const existingIds=products.map(p=>p.id);
      let html='<table class="table table-sm"><thead><tr><th>Product</th><th>Current Qty</th><th>New Qty</th></tr></thead><tbody>';
      if(products.length){
        products.forEach(p=>{
          html+=`<tr><td><div class="d-flex align-items-center gap-2">${p.image?`<img src="${p.image}" class="rounded" style="width:40px;height:40px;object-fit:cover">`:''}<span>${p.name}</span></div></td><td>${p.current_quantity}</td><td><input type="number" class="form-control form-control-sm restock-qty" data-product-id="${p.id}" value="${p.current_quantity}" min="0" style="width:80px"></td></tr>`;
        });
      }else{html+='<tr><td colspan="3" class="text-muted text-center">No products in this inventory</td></tr>';}
      html+='</tbody></table>';
      document.getElementById('restockProductsList').innerHTML=html;
      renderAddProducts(existingIds);
    }else{document.getElementById('restockProductsList').innerHTML='<p class="text-danger">Error loading products</p>';}
  }).catch(e=>{console.error(e);document.getElementById('restockProductsList').innerHTML='<p class="text-danger">Error loading products</p>';});
}
function renderAddProducts(existingIds){
  if(!storeProducts.length){
    document.getElementById('addProductsList').innerHTML='<p class="text-danger">Error: Could not load store products. Please refresh the page.</p>';
    return;
  }
  const available=storeProducts.filter(p=>!existingIds.includes(p.id));
  if(!available.length){document.getElementById('addProductsList').innerHTML='<p class="text-muted">No new products available</p>';return;}
  let html='<div class="row g-2">';
  available.forEach(p=>{
    const discountBadge=p.has_discount?`<span class="badge bg-danger ms-1">-${p.discount_percent}%</span>`:'';
    const priceDisplay=p.has_discount?`<span class="text-decoration-line-through text-muted small">$${p.original_price}</span> <span class="fw-bold text-success">$${p.discounted_price}</span>`:`<span class="fw-bold">$${p.price}</span>`;
    const tagsHtml=p.tags?p.tags.map(t=>`<span class="badge bg-secondary me-1">${t}</span>`).join(''):'';
    html+=`<div class="col-md-6"><div class="card product-card cursor-pointer h-100" onclick="toggleAddProduct(this,${p.id})"><div class="card-body p-2"><div class="d-flex gap-2"><input type="checkbox" class="form-check-input add-product-check mt-1" data-id="${p.id}">${p.image?`<img src="${p.image}" style="width:60px;height:60px;object-fit:cover" class="rounded flex-shrink-0">`:''}<div class="flex-grow-1 min-width-0"><div class="fw-semibold text-truncate">${p.name}</div><div class="small text-muted">${p.category||'No Category'}</div><div class="small mb-1">${tagsHtml}</div><div class="d-flex align-items-center">${priceDisplay}${discountBadge}</div></div></div><input type="number" class="form-control form-control-sm add-product-qty mt-2" data-id="${p.id}" value="0" min="0" placeholder="Quantity" style="display:none"></div></div></div>`;
  });
  html+='</div>';
  document.getElementById('addProductsList').innerHTML=html;
}
function toggleAddProduct(card,id){
  const check=card.querySelector('.add-product-check'),qty=card.querySelector('.add-product-qty');
  check.checked=!check.checked;card.classList.toggle('border-primary',check.checked);qty.style.display=check.checked?'block':'none';if(check.checked)qty.focus();
}
function saveRestock(){
  const id=document.getElementById('restockInventoryId').value;
  
  // Collect quantities for existing products (Restock Products tab)
  const quantities={};
  document.querySelectorAll('.restock-qty').forEach(input=>{
    const qty=parseInt(input.value)||0;
    if(qty>=0) quantities[input.dataset.productId]=qty;
  });
  console.log('Restock quantities:',quantities);
  
  // Collect selected products with quantities (Add Product tab)
  const addProducts={};
  const checks=document.querySelectorAll('.add-product-check:checked');
  console.log('Checked products:',checks.length);
  checks.forEach(c=>{
    const qtyInput=document.querySelector(`.add-product-qty[data-id="${c.dataset.id}"]`);
    const qty=qtyInput?parseInt(qtyInput.value)||0:0;
    if(qty>0){
      addProducts[c.dataset.id]=qty;
      console.log('Adding product',c.dataset.id,'with qty',qty);
    }
  });
  
  const promises=[];
  
  // Update existing products quantities
  if(Object.keys(quantities).length){
    promises.push(fetch('{{url("inventory")}}/'+id+'/restock',{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
      },
      body:JSON.stringify({quantities})
    }));
  }
  
  // Add new products to inventory
  if(Object.keys(addProducts).length){
    console.log('Sending addProducts:',addProducts);
    promises.push(fetch('{{url("inventory")}}/'+id+'/products',{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
      },
      body:JSON.stringify({products:addProducts})
    }));
  }
  
  Promise.all(promises)
    .then(responses=>{
      console.log('Save responses:',responses);
      restockModal.hide();
      location.reload();
    })
    .catch(e=>{
      console.error('Save error:',e);
      alert('Error saving changes. Please try again.');
    });
}
</script>
@endsection
