@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Payments</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $data->client->name }} - {{ $data->client->email }}</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
		            <h4 class="card-title">Payment Information</h4>
		        </div>
				<div class="card-body">
					<div class="basic-form">
						<div class="form" method="post">
							<div class="box-body">
								<div class="row">
								    <div class="col-md-12">
										<div class="form-group mb-3 mt-0">
										    <div class="alert alert-primary btn-block d-flex justify-content-between align-items-center">
										        <a href="{{ route('pay', [$data->unique_id]) }}" target="_blank" class="">{{ route('pay', [$data->unique_id]) }}</a>
										        <span class="badge badge-primary" onclick="withJquery('{{ route('pay', [$data->unique_id]) }}')" style="cursor: pointer;">COPY LINK</span>
										    </div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" required value="{{ $data->client->name }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">E-mail</label>
											<input type="email" class="form-control" name="email" required value="{{ $data->client->email }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Contact Number</label>
											<input type="text" class="form-control" name="phone" required value="{{ $data->client->phone }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Brand Name</label>
											<input type="text" class="form-control" name="brand_name" required value="{{ $data->client->brand->name }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Package Name</label>
											<input type="text" class="form-control" name="package" required value="{{ $data->package }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Amount ($)</label>
											<input type="number" class="form-control" name="l_name" required step="any" value="{{ $data->price }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Merchant</label>
											<input type="text" class="form-control" name="brand_name" required value="{{ $data->merchants->name }} - {{ $data->merchants->getMerchant() }}">
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group mb-3">
											<label class="form-label">Discription</label>
											<textarea class="form-control" name="description" id="description" cols="30" rows="2">{{ $data->description }}</textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		    </div>
		    <!-- /.box -->			
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">Invoice Items</h4>
				</div>
				<div class="card-body">
					@if($data->items->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Description</th>
									<th>Fee Amount ($)</th>
									<th>Fee Code</th>
								</tr>
							</thead>
							<tbody>
								@foreach($data->items as $index => $item)
								<tr>
									<td>{{ $index + 1 }}</td>
									<td>{{ $item->name }}</td>
									<td>{{ $item->description }}</td>
									<td>{{ number_format($item->fee_amount, 2) }}</td>
									<td>{{ $item->fee_code }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@else
						<div class="alert alert-warning">No invoice items found for this payment.</div>
					@endif
				</div>
			</div>
		</div>

	</div>
</div>

@endsection

@push('scripts')
<script>
    function withJquery(link){
	    var temp = $("<input>");
        $("body").append(temp);
        temp.val(link).select();
        document.execCommand("copy");
        temp.remove();
        console.timeEnd('time1');
    }
</script>
@endpush