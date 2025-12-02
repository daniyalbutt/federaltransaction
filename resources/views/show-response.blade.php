@extends('layouts.front-app')
@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Invoice #{{ $data->id }}</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Show Response</a></li>
		</ol>
	</div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Response <strong>Details</strong></h4>
                </div>
                <div class="card-body">
                    @php
                        $return_data = json_decode($data->return_response, true);
                        $payment_data = json_decode($data->payment_data, true);
                        $authorize_data = json_decode($data->authorize_response, true);
                        function displayData($data, $level = 0) {
                            $output = '';
                            foreach($data as $key => $value) {
                                $indent = str_repeat('&nbsp;', $level * 4);
                                $output .= '<div class="mb-2">';
                                $output .= $indent . '<strong class="text-capitalize">' . str_replace('_', ' ', $key) . ':</strong> ';
                                
                                if(is_array($value)) {
                                    $output .= '<div class="ms-4">' . displayData($value, $level + 1) . '</div>';
                                } else {
                                    $output .= '<span>' . ($value ?? 'N/A') . '</span>';
                                }
                                
                                $output .= '</div>';
                            }
                            return $output;
                        }
                    $paymentArray = json_decode(json_encode($return_data), true);
                    $paymentDataArray = json_decode(json_encode($payment_data), true);
                    $authorizeDataArray = json_decode(json_encode($authorize_data), true);
                    @endphp
                    @if(is_array($paymentDataArray))
                    {!! displayData($paymentDataArray) !!}
                    @endif
                    @if($data->status == 2)
                    @if($return_data != null)
                    @if(is_array($paymentArray))
                        <hr>
                        {!! displayData($paymentArray) !!}
                    @endif
                    @endif
                    @if($authorize_data != null)
                        <hr>
                        {!! displayData($authorizeDataArray) !!}
                    @endif
                    @endif

                    @if($data->return_response != null)
                    <hr>
                    <strong>{{ $data->return_response }}</strong>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@endpush