@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Merchants</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Create Merchant</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
					<h4 class="card-title">Merchant Form</h4>
		        </div>
		        <!-- /.box-header -->
				<div class="card-body">
					<div class="basic-form">
						<form class="form" method="post" action="{{ route('merchant.store') }}" enctype="multipart/form-data">
							@csrf
							<div class="box-body">
								@if($errors->any())
									{!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
								@endif
								@if(session()->has('success'))
								<div class="alert alert-success">
									{{ session()->get('success') }}
								</div>
								@endif
								<div class="row">
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" required value="{{ old('name') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Type</label>
											<select name="type" id="type" class="form-control" required>
												<option value="">Select Merchant</option>
												<option value="0">STRIPE</option>
												<option value="4">AUTHORIZE</option>
												<option value="5">PAYPAL</option>
												<!-- <option value="3">FETCH</option> -->
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Status</label>
											<select name="status" id="status" class="form-control" required>
												<option value="0">Active</option>
												<option value="1">Deactive</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group mb-3">
											<label class="form-label">Publishable key / Login ID / Client ID</label>
											<input type="text" class="form-control" name="public_key" value="{{ old('public_key') }}">
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group mb-3">
											<label class="form-label">Secret key / Transaction Key / Client Secret</label>
											<input type="text" class="form-control" name="private_key" value="{{ old('private_key') }}">
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group mb-3">
											<label class="form-label">Production/Sandbox</label>
											<select name="sandbox" id="sandbox" class="form-control">
												<option value="0">Production</option>
												<option value="1">Sandbox</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<!-- /.box-body -->
							<div class="box-footer">
								<button type="button" class="btn btn-warning me-1">
								<i class="ti-trash"></i> Cancel
								</button>
								<button type="submit" class="btn btn-primary">
								<i class="ti-save-alt"></i> Save
								</button>
							</div>
						</form>
					</div>
				</div>
		    </div>
		    <!-- /.box -->			
		</div>
	</div>
</div>
@endsection

@push('scripts')
@endpush