@extends('layouts.front-app')
@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Edit User - {{ $data->name }}</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
					<h4 class="card-title">Edit User Form - {{ $data->name }}</h4>
		        </div>
		        <!-- /.box-header -->
				<div class="card-body">
					<div class="basic-form">
						<form class="form" method="post" action="{{ route('users.update', $data->id) }}">
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
									<div class="col-md-3">
										<div class="form-group">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" value="{{ old('name', $data->name) }}" required>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="form-label">E-mail</label>
											<input type="email" class="form-control" name="email" value="{{ old('email', $data->email) }}" required>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="form-label">Role</label>
											<select name="role" id="role" class="form-control" required>
												@foreach($roles as $key => $value)
												<option value="{{ $value->name }}" {{ $data->getRole() == $value->name ? 'selected' : '' }}>{{ $value->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label class="form-label">Password</label>
											<input type="text" class="form-control" name="password">
										</div>
									</div>
								</div>
							</div>
							<!-- /.box-body -->
							<div class="box-footer mt-3">
								<button type="button" class="btn btn-warning me-1">
								<i class="ti-trash"></i> Cancel
								</button>
								<button type="submit" class="btn btn-primary">
								<i class="ti-save-alt"></i> Update
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