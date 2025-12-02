@extends('layouts.front-app')
@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Roles</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Add Role</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
					<h4 class="card-title">Add Role</h4>
		        </div>
		        <!-- /.box-header -->
				<div class="card-body">
					<div class="basic-form">
						<form class="form" method="post" action="{{ route('roles.store') }}">
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
									<div class="col-md-3">
										<div class="form-group">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<ul class="role-wrapper">
										@foreach($permission as $key => $value)
											<li>
												<input name="permission[]" value="{{ $value->name }}" type="checkbox" id="basic_checkbox_{{$key}}"/>
												<label for="basic_checkbox_{{$key}}">{{ $value->name }}</label>
											</li>
										@endforeach
										</ul>
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
		</div>
	</div>
</div>
@endsection

@push('scripts')
@endpush