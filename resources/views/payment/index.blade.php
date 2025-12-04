@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Payments</a></li>
		</ol>
	</div>
	<div class="row page-titles p-0 pt-3 pb-3">
		<div class="col-md-12">
			<form method="get" action="{{ route('payment.index') }}">
				<div class="box">
					<div class="box-header">
						<div class="row">
							<div class="col-md">
								<input type="text" name="name" class="form-control" placeholder="Name" value="{{ Request::get('name') }}">
							</div>
							<div class="col-md">
								<input type="text" name="email" class="form-control" placeholder="Email" value="{{ Request::get('email') }}">
							</div>
							<div class="col-md">
								<input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ Request::get('phone') }}">
							</div>
							<div class="col-md">
								<select class="form-control" name="status">
									<option value="" {{ Request::get('status') == null ? 'selected' : '' }}>All</option>
									<option value="2" {{ !is_null(Request::get('status')) && Request::get('status') == 2 ? 'selected' : '' }}>SUCCESS</option>
									<option value="0" {{ !is_null(Request::get('status')) && Request::get('status') == 0 ? 'selected' : '' }}>PENDING</option>
									<option value="1" {{ !is_null(Request::get('status')) && Request::get('status') == 1 ? 'selected' : '' }}>DECLINED</option>
								</select>
							</div>
							<div class="col-md-2">
								<button class="btn btn-primary" type="submit" style="width: 100%;">Search</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">Payments</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-responsive-md">
							<thead>
								<tr>
									<th><strong>CUSTOMER</strong></th>
									<th><strong>PACKAGE / PRICE</strong></th>
									<th><strong>BRAND / MERCHANT</strong></th>
									<th><strong>STATUS</strong></th>
									<th><strong>CREATED AT</strong></th>
									<th class="text-end"><strong>Action</strong></th>
								</tr>
							</thead>
							<tbody>
								@foreach($data as $key => $value)
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="w-space-no">{{ $value->client->name }} <br> {{ $value->client->email }}</span>
										</div>
									</td>
									<td>
										{{ $value->package }}<br>${{ $value->price }}
									</td>
									<td><span class="badge light badge-info">{{ $value->client->brand->name }}</span> <br> <span class="badge light badge-secondary mt-1">{{ $value->merchants != null ? $value->merchants->name : '' }} - {{ $value->merchants != null ? $value->merchants->getMerchant() : '' }}</span></td>
									<td>
										<span class="badge light {{ $value->get_badge_status() }}">{{ $value->get_status() }}</span>
										@can('mark as paid')
										@if($value->status == 0)
										<br>
										<a href="javascript:;" data-id="{{ $value->id }}" class="btn btn-danger btn-xs p-2 pt-1 pb-1 mt-1 text-uppercase mark-as-paid">Mark as Paid</a>
										@endif
										@endcan
									</td>
									<td>{{ $value->created_at->format('d M, Y g:i A') }}</td>
									<!-- <td>{{ $value->updated_at->format('d M, Y g:i A') }}</td> -->
									<td class="text-end">
										<span class="badge badge-primary" onclick="withJquery('{{ route('pay', [$value->unique_id]) }}')" style="cursor: pointer;">COPY LINK</span><br>
										<div class="d-flex justify-content-end mt-2">
											@if(($value->status == 2) || ($value->status == 1))
											<a href="{{ route('show.response', $value->id) }}" class="btn btn-info shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
											@endif
											@can('edit payment')
											<a href="{{ route('payment.edit', $value->id) }}" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
											@endcan
											@can('payment')
											<a href="{{ route('payment.show', $value->id) }}" class="btn btn-info shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
											@endcan
											@can('delete payment')
											<a onclick="return confirm('Are you sure?')"  href="{{ route('payment.delete', ['id' => $value->id]) }}" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
											@endcan
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{ $data->appends(request()->except('page'))->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@can('mark as paid')
<!-- Modal -->
<div class="modal fade" id="markAddPaidModal" tabindex="-1" aria-labelledby="markAddPaidLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
		<form action="{{ route('payment.paid') }}" class="w-100 mark-as-paid-form">
			<input type="hidden" name="payment_id" class="payment_id">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="markAddPaidLabel"></h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body text-center">
					<h2>Are you sure you want to <br>pay this invoice?</h2>
					<textarea placeholder="Enter source of payment" name="source" id="source" class="form-control mt-3 source"></textarea>
				</div>
				<div class="modal-footer justify-content-center">
					<button type="button" class="btn btn-secondary btn-xs" data-bs-dismiss="modal">NO</button>
					<button type="submit" class="btn btn-primary btn-xs">YES</button>
				</div>
			</div>
		</form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
	var a;
    function withJquery(link){
	    var temp = $("<input>");
        $("body").append(temp);
        temp.val(link).select();
        document.execCommand("copy");
        temp.remove();
        console.timeEnd('time1');
    }
</script>
<script>
	$(document).ready(function () {
    	$(".mark-as-paid").on("click", function () {
			a = $(this);
			var id = $(this).data('id');
			$('.mark-as-paid-form').find('.payment_id').val(id);
      		var modal = new bootstrap.Modal(document.getElementById('markAddPaidModal'));
      		modal.show();
    	});

		$('.mark-as-paid-form').submit(function(e){
			e.preventDefault();
			var source = $(this).find('.source').val();
			var id = $(this).find('.payment_id').val();
			$.ajax({
				url: $(this).attr('action'),
        		method: "POST",
				data: {
					source: source,
					id: id,
					_token: jQuery('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					$(a).parent().find('span').removeClass().addClass('badge light btn-success');
					$(a).remove();
					var modal = bootstrap.Modal.getInstance(document.getElementById('markAddPaidModal'));
					if (modal) modal.hide();
				},
				error: function(xhr) {
					console.log(xhr);
				}
			});
		});
  	});
</script>
@endpush