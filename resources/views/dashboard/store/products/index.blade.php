@extends('layout.dashboard')
@section('title','My products')
@section('content_header')
<x-page-heading title="My products" :breadcrumb-items="[['label'=>'Dashboard','url'=>route('dashboard')],['label'=>'Products']]" :add-route="route('products.create')" add-label="Add product"/>
@endsection
@section('page_title','Products')
@section('content')
@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{session('success')}}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
<div class="card shadow-sm mb-3">
  <div class="card-body py-3">
    <form method="get" action="{{route('products.index')}}" class="row g-2 align-items-end">
      <div class="col-lg-3 col-md-6"><label class="form-label small mb-1">{{__('Category')}}</label><select name="category_id" class="form-select">@foreach($categoryFilterOptions??[] as $opt)<option value="{{$opt['value']}}" @selected(($filters['category_id']??'')===(string)$opt['value'])>{{$opt['label']}}</option>@endforeach</select></div>
      <div class="col-lg-3 col-md-6"><label class="form-label small mb-1">{{__('Status')}}</label><select name="status" class="form-select">@foreach($statusFilterOptions??[] as $opt)<option value="{{$opt['value']}}" @selected(($filters['status']??'')===(string)$opt['value'])>{{$opt['label']}}</option>@endforeach</select></div>
      <div class="col-lg col-md-6"><label class="form-label small mb-1">{{__('Search')}}</label><input type="search" name="search" value="{{$filters['search']??''}}" class="form-control" placeholder="{{__('Type to search…')}}"></div>
      <div class="col-lg-2 col-md-6"><label class="form-label small mb-1">{{__('Tag')}}</label><select name="tag" class="form-select">@foreach($tagFilterOptions??[] as $opt)<option value="{{$opt['value']}}" @selected(($filters['tag']??'')===$opt['value'])>{{$opt['label']}}</option>@endforeach</select></div>
      <div class="col-auto d-flex gap-2 align-items-end"><button type="submit" class="btn btn-primary">{{__('Filter')}}</button><a href="{{route('products.index')}}" class="btn btn-outline-secondary">{{__('Reset')}}</a><button type="button" id="bulkArchiveBtn" class="btn btn-outline-warning" onclick="toggleBulkMode()"><i class="bi bi-archive"></i> <span id="bulkBtnText">{{__('Archive')}}</span></button></div>
    </form>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body p-0">
    <form id="bulkForm" method="POST" action="{{route('products.bulk-archive')}}" style="display:none;">@csrf<div id="selectedProductsContainer"></div></form>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="bulk-checkbox" style="display:none;width:40px;"><input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll()"></th><th>#</th><th>Image</th><th>Name</th><th>Tags</th><th>Main Price</th><th>Discount Price</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse($products as $p)
          <tr data-product-id="{{$p->id}}" onclick="toggleRowSelection(this)" data-product-name="{{$p->name}}" data-product-image="{{$p->image?asset('storage/'.$p->image):''}}" data-product-price="{{$p->price}}" data-product-category="{{$p->category?->name??'N/A'}}" data-product-tags="{{$p->tag_list}}" data-discount-id="{{$p->discount?->id??''}}" data-discount-price="{{$p->discount?->discount_price??''}}" data-discount-start="{{$p->discount?->start_date??''}}" data-discount-end="{{$p->discount?->end_date??''}}">
            <td class="bulk-checkbox" style="display:none;"><input type="checkbox" class="form-check-input product-checkbox" name="product_ids[]" value="{{$p->id}}" onclick="event.stopPropagation();updateBulkButton();"></td>
            <td>{{$p->id}}</td>
            <td>@if($p->image)<img src="{{asset('storage/'.$p->image)}}" class="img-fluid rounded border" style="max-width:72px;max-height:72px;object-fit:contain">@else—@endif</td>
            <td>{{$p->name}}</td>
            <td>{{$p->tag_list}}</td>
            <td><span class="fw-semibold">{{number_format($p->price,2)}}</span></td>
            <td>@if($p->discount)<span class="badge bg-success fs-6">{{number_format($p->discount->discount_price,2)}}</span><div class="small text-muted mt-1"><s>{{number_format($p->price,2)}}</s> <span class="text-success">-{{number_format((($p->price-$p->discount->discount_price)/$p->price)*100,0)}}%</span></div>@else<span class="text-muted">—</span>@endif</td>
            <td><span class="badge bg-{{$p->status==='active'?'success':($p->status==='disactive'?'danger':'secondary')}}">{{ucfirst($p->status)}}</span></td>
            <td class="text-end text-nowrap">
              <a href="{{route('products.show',$p)}}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
              <a href="{{route('products.edit',$p)}}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
              <button type="button" class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation();toggleStatus({{$p->id}})" title="Toggle"><i class="bi bi-arrow-repeat"></i></button>
              <button type="button" class="btn btn-sm btn-outline-success" onclick="event.stopPropagation();openDiscountModal({{$p->id}})" title="Discount"><i class="bi bi-percent"></i></button>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation();deleteProduct({{$p->id}})" title="Delete"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          @empty<tr><td colspan="8" class="text-center text-secondary py-4">No products found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <x-pagination-footer :paginator="$products"/>
</div>

  <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Product Discount</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div id="productDetails" class="mb-4"></div>
          <form id="discountForm">@csrf<input type="hidden" id="discountProductId"><input type="hidden" id="discountId"><input type="hidden" id="discountToRemove">
            <div id="discountErrorAlert" class="alert alert-danger" style="display:none;"><ul id="discountErrorList" class="mb-0"></ul></div>
            <div class="mb-3"><label class="form-label">Discount Price <span class="text-danger">*</span></label><input type="number" step="0.01" class="form-control" id="discountPrice" required><div class="invalid-feedback" id="discountPriceError"></div><div class="form-text">Current: <span id="currentPrice"></span></div></div>
            <div class="mb-3"><label class="form-label">Start <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" id="startDate" required><div class="invalid-feedback" id="startDateError"></div></div>
            <div class="mb-3"><label class="form-label">End <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" id="endDate" required><div class="invalid-feedback" id="endDateError"></div></div>
            <div class="alert alert-info" id="discountPreview" style="display:none;"><strong>Preview:</strong> <span id="originalPrice"></span> → <span id="discountPricePreview"></span> (Save: <span id="savingsAmount"></span>)</div>
          </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="deleteDiscountBtn" style="display:none;" onclick="deleteDiscount()">Remove</button><button type="button" class="btn btn-primary" onclick="saveDiscount()">Save</button></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0"><h5 class="modal-title" id="confirmModalTitle">Confirm</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body text-center py-3"><i id="confirmModalIcon" class="bi bi-exclamation-circle text-warning mb-3" style="font-size: 3rem;"></i><p class="fs-5" id="confirmModalMessage">Are you sure?</p></div>
        <div class="modal-footer border-0 justify-content-center"><button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger px-4" id="confirmModalBtn" onclick="executeConfirmAction()">Confirm</button></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Change Status</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><input type="hidden" id="statusProductId"><div class="mb-3"><label class="form-label">Select Status <span class="text-danger">*</span></label><select id="statusSelect" class="form-select"><option value="active">Active</option><option value="disactive">Disactive</option><option value="draft">Draft</option></select></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" onclick="saveStatus()">Save</button></div>
      </div>
    </div>
  </div>

  <script>
    let discountModal,confirmModal,statusModal,currentProduct=null,confirmAction=null,currentProductId=null,bulkMode=false;
    document.addEventListener('DOMContentLoaded',function(){
      discountModal=new bootstrap.Modal(document.getElementById('discountModal'));
      confirmModal=new bootstrap.Modal(document.getElementById('confirmModal'));
      statusModal=new bootstrap.Modal(document.getElementById('statusModal'));
      document.getElementById('discountPrice')?.addEventListener('input',function(){clearFieldError('discountPrice');updateDiscountPreview();});
      document.getElementById('startDate')?.addEventListener('input',()=>clearFieldError('startDate'));
      document.getElementById('endDate')?.addEventListener('input',()=>clearFieldError('endDate'));
    });
    function clearFieldError(id){const f=document.getElementById(id);if(f){f.classList.remove('is-invalid');const e=document.getElementById(id+'Error');if(e)e.textContent='';}}
    function clearDiscountErrors(){document.getElementById('discountErrorAlert').style.display='none';document.getElementById('discountErrorList').innerHTML='';['discountPrice','startDate','endDate'].forEach(clearFieldError);}
    function showDiscountError(f,m){if(f&&document.getElementById(f)){document.getElementById(f).classList.add('is-invalid');document.getElementById(f+'Error').textContent=m;}const l=document.getElementById('discountErrorList');const i=document.createElement('li');i.textContent=m;l.appendChild(i);document.getElementById('discountErrorAlert').style.display='block';}
    function validateDiscountForm(){
      clearDiscountErrors();let v=true;
      const dp=parseFloat(document.getElementById('discountPrice').value),sd=document.getElementById('startDate').value,ed=document.getElementById('endDate').value,op=currentProduct?currentProduct.price:0;
      if(!document.getElementById('discountPrice').value){showDiscountError('discountPrice','Required');v=false;}
      else if(isNaN(dp)||dp<0){showDiscountError('discountPrice','Positive number required');v=false;}
      if(!sd){showDiscountError('startDate','Required');v=false;}
      if(!ed){showDiscountError('endDate','Required');v=false;}
      if(sd&&ed&&new Date(ed)<=new Date(sd)){showDiscountError('endDate','Must be after start');v=false;}
      if(op>0&&!isNaN(dp)){const pct=((op-dp)/op)*100;if(pct>60){showDiscountError('discountPrice',`Max 60% off. Max price: $${(op*0.4).toFixed(2)}`);v=false;}if(dp>=op){showDiscountError('discountPrice','Must be less than original');v=false;}}
      return v;
    }
    function openDiscountModal(pid){
      const r=document.querySelector(`tr[data-product-id="${pid}"]`);if(!r)return;
      currentProduct={id:pid,name:r.dataset.productName,image:r.dataset.productImage,price:parseFloat(r.dataset.productPrice),category:r.dataset.productCategory,tags:r.dataset.productTags};
      clearDiscountErrors();
      document.getElementById('discountProductId').value=pid;document.getElementById('currentPrice').textContent='$'+currentProduct.price.toFixed(2);
      const did=r.dataset.discountId;
      if(did){document.getElementById('discountId').value=did;document.getElementById('discountPrice').value=r.dataset.discountPrice;document.getElementById('startDate').value=r.dataset.discountStart;document.getElementById('endDate').value=r.dataset.discountEnd;document.getElementById('deleteDiscountBtn').style.display='inline-block';}
      else{document.getElementById('discountId').value='';document.getElementById('discountPrice').value='';document.getElementById('startDate').value='';document.getElementById('endDate').value='';document.getElementById('deleteDiscountBtn').style.display='none';}
      document.getElementById('productDetails').innerHTML=`<div class="d-flex align-items-center mb-3">${currentProduct.image?`<img src="${currentProduct.image}" class="me-3" style="max-width:80px;max-height:80px;object-fit:contain">`:''}<div><h6 class="mb-1">${currentProduct.name}</h6><p class="mb-0 text-muted">${currentProduct.category}</p><p class="mb-0 text-muted">${currentProduct.tags}</p></div></div>`;
      updateDiscountPreview();discountModal.show();
    }
    function updateDiscountPreview(){const dp=parseFloat(document.getElementById('discountPrice').value);if(dp&&currentProduct){document.getElementById('originalPrice').textContent='$'+currentProduct.price.toFixed(2);document.getElementById('discountPricePreview').textContent='$'+dp.toFixed(2);document.getElementById('savingsAmount').textContent='$'+(currentProduct.price-dp).toFixed(2);document.getElementById('discountPreview').style.display='block';}else{document.getElementById('discountPreview').style.display='none';}}
    function saveDiscount(){
      if(!validateDiscountForm())return;
      const did=document.getElementById('discountId').value,url=did?`{{ route('discounts.update', ':id') }}`.replace(':id',did):`{{ route('products.discount.store', ':id') }}`.replace(':id',document.getElementById('discountProductId').value);
      fetch(url,{method:did?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify({discount_price:parseFloat(document.getElementById('discountPrice').value),start_date:document.getElementById('startDate').value,end_date:document.getElementById('endDate').value})})
      .then(async r=>{const d=await r.json();if(!r.ok){if(r.status===422&&d.errors){const m={'discount_price':'discountPrice','start_date':'startDate','end_date':'endDate'};Object.keys(d.errors).forEach(f=>showDiscountError(m[f]||f,d.errors[f][0]));return null;}throw new Error(d.message||'Failed');}return d;})
      .then(d=>{if(!d)return;if(d.success){discountModal.hide();location.reload();}else{showDiscountError(null,d.message||'Error');}})
      .catch(e=>{console.error(e);showDiscountError(null,'Failed to save');});
    }
    function deleteDiscount(){const did=document.getElementById('discountId').value;if(!did)return;document.getElementById('discountToRemove').value=did;showConfirmModal('Remove Discount','Remove this discount?','removeDiscount',null,'bi-x-circle text-danger','btn-danger');}
    function doRemoveDiscount(){const did=document.getElementById('discountToRemove').value;fetch(`{{ route('discounts.destroy', ':id') }}`.replace(':id',did),{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}}).then(r=>r.json()).then(d=>{if(d.success){discountModal.hide();location.reload();}else alert('Error: '+d.message);}).catch(e=>alert('Error'));}    
    function showConfirmModal(t,m,a,pid,ic,bc){document.getElementById('confirmModalTitle').textContent=t;document.getElementById('confirmModalMessage').textContent=m;document.getElementById('confirmModalIcon').className='bi '+ic;document.getElementById('confirmModalBtn').className='btn '+bc+' px-4';confirmAction=a;currentProductId=pid;confirmModal.show();}
    function executeConfirmAction(){if(confirmAction==='delete')doDeleteProduct(currentProductId);else if(confirmAction==='removeDiscount')doRemoveDiscount();confirmModal.hide();}
    function toggleStatus(pid){document.getElementById('statusProductId').value=pid;statusModal.show();}
    function saveStatus(){const pid=document.getElementById('statusProductId').value,status=document.getElementById('statusSelect').value;fetch(`{{ route('products.toggle-status', ':id') }}`.replace(':id',pid),{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({status:status})}).then(r=>r.json()).then(d=>{if(d.success){statusModal.hide();location.reload();}else alert('Error: '+d.message);}).catch(e=>alert('Error'));}    
    function deleteProduct(pid){showConfirmModal('Delete Product','Delete this product?','delete',pid,'bi-trash text-danger','btn-danger');}
    function doDeleteProduct(pid){const f=document.createElement('form');f.method='POST';f.action=`{{ route('products.destroy', ':id') }}`.replace(':id',pid);f.innerHTML=`<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="DELETE">`;document.body.appendChild(f);f.submit();}
    function toggleBulkMode(){if(!bulkMode){bulkMode=true;document.querySelectorAll('.bulk-checkbox').forEach(e=>e.style.display='');document.getElementById('bulkForm').style.display='';document.getElementById('bulkBtnText').textContent='{{ __('Archive') }}';document.getElementById('bulkArchiveBtn').classList.remove('btn-outline-warning');document.getElementById('bulkArchiveBtn').classList.add('btn-warning');}else{const s=document.querySelectorAll('.product-checkbox:checked');if(s.length===0){bulkMode=false;document.querySelectorAll('.bulk-checkbox').forEach(e=>e.style.display='none');document.getElementById('bulkForm').style.display='none';document.getElementById('bulkBtnText').textContent='{{ __('Archive') }}';document.getElementById('bulkArchiveBtn').classList.add('btn-outline-warning');document.getElementById('bulkArchiveBtn').classList.remove('btn-warning');return;}const c=document.getElementById('selectedProductsContainer');c.innerHTML='';s.forEach(cb=>{const i=document.createElement('input');i.type='hidden';i.name='product_ids[]';i.value=cb.value;c.appendChild(i);});document.getElementById('bulkForm').submit();}}
    function toggleSelectAll(){const a=document.getElementById('selectAll').checked;document.querySelectorAll('.product-checkbox').forEach(cb=>cb.checked=a);updateBulkButton();}
    function toggleRowSelection(r){if(!bulkMode)return;const cb=r.querySelector('.product-checkbox');if(cb){cb.checked=!cb.checked;updateBulkButton();}}
    function updateBulkButton(){const s=document.querySelectorAll('.product-checkbox:checked').length;document.getElementById('bulkBtnText').textContent=s>0?'{{ __('Save') }} ('+s+')':'{{ __('Archive') }}';}
  </script>
@endsection
