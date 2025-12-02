@extends('layouts.front-app')
@section('content')

@php
$editable = $data->status == 0; // editable only if PENDING
@endphp

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
                    <h4 class="card-title">Payment Information 
                        @if(!$editable)
                            <span class="badge badge-warning">Read Only (Status: {{ $data->get_status() }})</span>
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form class="form" method="post" action="{{ $editable ? route('payment.update', $data->id) : '#' }}">
                            @csrf
                            @if($editable)
                                @method('PUT')
                            @endif

                            <div class="box-body">
                                @if($errors->any())
                                    {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3 mt-0">
                                            <div class="alert alert-primary btn-block d-flex justify-content-between align-items-center">
                                                <a href="{{ route('pay', [$data->unique_id]) }}" target="_blank">{{ route('pay', [$data->unique_id]) }}</a>
                                                <span class="badge badge-primary" onclick="withJquery('{{ route('pay', [$data->unique_id]) }}')" style="cursor: pointer;">COPY LINK</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $data->client->name) }}" {{ $editable ? '' : 'disabled' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">E-mail</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', $data->client->email) }}" {{ $editable ? '' : 'disabled' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Contact Number</label>
                                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $data->client->phone) }}" {{ $editable ? '' : 'disabled' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Brand Name</label>
                                            <select name="brand_name" class="form-control" {{ $editable ? '' : 'disabled' }}>
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ old('brand_name', $data->client->brand_name) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Package Name</label>
                                            <input type="text" class="form-control" name="package" value="{{ old('package', $data->package) }}" {{ $editable ? '' : 'disabled' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Amount ($)</label>
                                            <input type="number" step="any" class="form-control" name="price" value="{{ old('price', $data->price) }}" {{ $editable ? '' : 'disabled' }}>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Merchant</label>
                                            <select name="merchant" class="form-control" {{ $editable ? '' : 'disabled' }}>
                                                @foreach($merhant as $m)
                                                    <option value="{{ $m->id }}" {{ $data->merchant == $m->id ? 'selected' : '' }}>{{ $m->name }} - {{ $m->getMerchant() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="2" {{ $editable ? '' : 'disabled' }}>{{ old('description', $data->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Invoice Items --}}
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h4 class="card-title">Invoice Items</h4>
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
                                                @foreach($data->items as $index => $item)
                                                <tr class="invoice-item-row">
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][name]" class="form-control item-name" value="{{ $item->name }}" {{ $editable ? '' : 'disabled' }} required>
                                                    </td>
                                                    <td>
                                                        <textarea name="items[{{ $index }}][description]" class="form-control item-description" {{ $editable ? '' : 'disabled' }}>{{ $item->description }}</textarea>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" name="items[{{ $index }}][fee_amount]" class="form-control item-fee-amount" value="{{ $item->fee_amount }}" {{ $editable ? '' : 'disabled' }} required>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][fee_code]" class="form-control item-fee-code" value="{{ $item->fee_code }}" {{ $editable ? '' : 'disabled' }}>
                                                    </td>
                                                    <td>
                                                        @if($editable)
                                                            <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if($editable)
                                            <div class="text-end">
                                                <button type="button" class="btn btn-success" id="add-item">Add Item</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="box-footer mt-4">
                                @if($editable)
                                    <button type="submit" class="btn btn-primary"><i class="ti-save-alt"></i> Save Payment</button>
                                @endif
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
<script>
    $(document).ready(function() {
        let editable = {{ $editable ? 'true' : 'false' }};
        if (!editable) return;

        let itemIndex = {{ $data->items->count() }};

        $('#add-item').click(function() {
            let newRow = `
                <tr class="invoice-item-row">
                    <td><input type="text" name="items[${itemIndex}][name]" class="form-control item-name" required></td>
                    <td><textarea name="items[${itemIndex}][description]" class="form-control item-description"></textarea></td>
                    <td><input type="number" step="any" name="items[${itemIndex}][fee_amount]" class="form-control item-fee-amount" required></td>
                    <td><input type="text" name="items[${itemIndex}][fee_code]" class="form-control item-fee-code"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                </tr>
            `;
            $('#invoice-items-table tbody').append(newRow);
            itemIndex++;
            attachAutocomplete();
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
        });

        function attachAutocomplete() {
            $('.item-name').autocomplete({
                source: "{{ route('items.autocomplete') }}",
                minLength: 1,
                select: function(event, ui) {
                    let row = $(this).closest('tr');
                    row.find('.item-description').val(ui.item.description);
                    row.find('.item-fee-amount').val(ui.item.fee_amount);
                    row.find('.item-fee-code').val(ui.item.fee_code);
                }
            });
        }
        
        attachAutocomplete();

    });

    function withJquery(link){
        var temp = $("<input>");
        $("body").append(temp);
        temp.val(link).select();
        document.execCommand("copy");
        temp.remove();
    }
</script>
@endpush
