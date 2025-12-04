@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Brand</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Brand - {{ $data->name }}</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
					<h4 class="card-title">Edit Brand Form - {{ $data->name }}</h4>
		        </div>
		        <!-- /.box-header -->
				<div class="card-body">
					<div class="basic-form">
						<form class="form" method="post" action="{{ route('brand.update', $data->id) }}" enctype="multipart/form-data">
							@csrf
							@method('PUT')
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
											<label class="form-label">Image</label>
											<input type="file" class="form-control" name="image">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Background Image</label>
											<input type="file" class="form-control" name="background_image">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" value="{{ $data->name }}" required value="{{ old('name') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Status</label>
											<select name="status" id="status" class="form-control">
												<option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Active</option>
												<option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Deactive</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Address First Line</label>
											<input type="text" class="form-control" name="address_first_line" value="{{ $data->address_first_line }}" required value="{{ old('address_first_line') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Address Second Line</label>
											<input type="text" class="form-control" name="address_second_line" value="{{ $data->address_second_line }}" required value="{{ old('address_second_line') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Address Third Line</label>
											<input type="text" class="form-control" name="address_third_line" value="{{ $data->address_third_line }}" required value="{{ old('address_third_line') }}">
										</div>
									</div>
								</div>
							</div>
							<!-- /.box-body -->
							<div class="box-footer">
								<button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Update Brand</button>
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