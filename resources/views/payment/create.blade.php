@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Invoices</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Create Invoice</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
					<h4 class="card-title">Invoice Form</h4>
		        </div>
		        <!-- /.box-header -->
				<div class="card-body">
					<div class="basic-form">
						<form class="form" method="post" action="{{ route('payment.store') }}">
							@csrf
							<div class="box-body">
								@if($errors->any())
									{!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
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
											<label class="form-label">E-mail</label>
											<input type="email" class="form-control" name="email" required value="{{ old('email') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Contact Number</label>
											<input type="text" class="form-control" name="phone" required value="{{ old('phone') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Brand Name</label>
											<select name="brand_name" id="brand_name" class="form-control" required>
												<option value="">Select Brand</option>
												@foreach($brands as $key => $value)
												<option value="{{ $value->id }}" {{ old('brand_name') == $value->id ? 'selected' : ' ' }}>{{ $value->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Package</label>
											<input type="text" class="form-control" name="package" required value="{{ old('package') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Tax Amount ($)</label>
											<input step="any" type="number" class="form-control" required="" name="tax_amount" value="{{ old('tax_amount', 0) }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Merchant</label>
											<select name="merchant" class="form-control" id="merchant" required>
												<!-- <option value="0">STRIPE</option> -->
												<!-- <option value="1">SQUARE</option>
												<option value="2">STRIPE - One Step Marketing</option> -->
												@foreach($merhant as $key => $value)
												<option value="{{ $value->id }}">{{ $value->name }} - {{ $value->getMerchant() }}</option>
												@endforeach
												<!-- <option value="3">FETCH</option> -->
												<!-- <option value="4">AUTHORIZE</option> -->
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group mb-3">
											<label class="form-label">Discription</label>
											<textarea class="form-control" name="description" id="description" cols="30" rows="2">{{ old('description') }}</textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<hr>
									<h4 class="card-title mt-4 mb-4">Invoice Items</h4>
									<hr>
									<table class="table table-striped" id="invoice-items-table">
										<thead>
											<tr>
												<th>Name</th>
												<th>Description</th>
												<th>Fee Amount ($)</th>
												<th>Fee Code</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr class="invoice-item-row">
												<td>
													<input type="text" name="items[0][name]" class="form-control item-name" required>
												</td>
												<td style="width: 44%;">
													<textarea name="items[0][description]" id="" class="form-control item-description"></textarea>
												</td>
												<td style="width: 10%;">
													<input type="number" step="any" name="items[0][fee_amount]" class="form-control item-fee-amount" required>
												</td>
												<td style="width: 10%;">
													<input type="text" name="items[0][fee_code]" class="form-control item-fee-code">
												</td>
												<td>
													<button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
												</td>
											</tr>
										</tbody>
									</table>
									<div class="text-end">
										<button type="button" class="btn btn-success" id="add-item">Add Item</button>
									</div>
								</div>
							</div>

							<!-- /.box-body -->
							<div class="box-footer mt-5">
								<button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Payment</button>
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
<script>
$(document).ready(function() {
    let itemIndex = 1;
	const sourceUrl = "{{ route('items.autocomplete') }}";

	function initAutocomplete($input) {
        if (typeof $.ui === 'undefined' || typeof $input.autocomplete !== 'function') {
            console.warn('jQuery UI Autocomplete not available. Make sure jQuery UI JS & CSS are loaded.');
            return;
        }

        $input.autocomplete({
            source: sourceUrl,
            minLength: 1,
            select: function(event, ui) {
                let row = $(this).closest('tr');
                row.find('.item-description').val(ui.item.description || '');
                row.find('.item-fee-amount').val(ui.item.fee_amount || '');
                row.find('.item-fee-code').val(ui.item.fee_code || '');
            }
        });
    }

	$('#invoice-items-table').find('.item-name').each(function() {
        initAutocomplete($(this));
    });


    $('#add-item').click(function() {
        let newRow = `
            <tr class="invoice-item-row">
                <td>
                    <input type="text" name="items[${itemIndex}][name]" class="form-control item-name" required>
                </td>
                <td>
                    <textarea name="items[${itemIndex}][description]" class="form-control item-description"></textarea>
                </td>
                <td>
                    <input type="number" step="any" name="items[${itemIndex}][fee_amount]" class="form-control item-fee-amount" required>
                </td>
                <td>
                    <input type="text" name="items[${itemIndex}][fee_code]" class="form-control item-fee-code">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </td>
            </tr>
        `;
        $('#invoice-items-table tbody').append(newRow);
        let $newInput = $('#invoice-items-table tbody tr').last().find('.item-name');
        initAutocomplete($newInput);
        itemIndex++;
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endpush
