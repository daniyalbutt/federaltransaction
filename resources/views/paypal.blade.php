@extends('layouts.payment-app')
@section('content')

@if ($data->status == 0)
<div class="container" style="height: 100vh;">
    <div class="row h-100">
        <div class="col-md-6 pr-0">
            <div class="payment-left">
                <div id="invoiceImageContainer"></div>
            </div>
        </div>
        <div class="col-md-6 pl-0">
            <div class="payment-right text-center paypal-wrapper">
                <h1>Pay with PayPal</h1>
                <h6>Complete your payment securely using PayPal</h6>
                <p>Amount: <strong>${{ number_format($data->price, 2) }}</strong></p>

                <div id="paypal-button-container"></div>

                <div id="loader" style="display: none; margin-top: 20px;">
                    <img src="{{ asset('images/loader.gif') }}" alt="Loading...">
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container mt-5">
    @if ($data->status == 2)
        <div class="alert alert-success text-center">
            <h3>Payment Successful!</h3>
            <p>Your payment has been completed.</p>
        </div>
    @elseif($data->status == 1)
        <div class="alert alert-danger text-center">
            <h3>Payment Declined</h3>
            <p>{{ $data->return_response }}</p>
        </div>
    @endif
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Determine client ID based on sandbox or production
        var paypalClientId = "{{ $data->merchants->public_key }}"; // PAYPAL_CLIENT_ID stored in merchants table

        // Load PayPal JS dynamically
        var script = document.createElement('script');
        script.src = "https://www.paypal.com/sdk/js?client-id=" + paypalClientId + "&currency=USD";
        script.onload = function () {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: "{{ number_format($data->price, 2, '.', '') }}"
                            },
                            description: "{{ $data->package }}"
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    document.getElementById('loader').style.display = 'block';
                    return actions.order.capture().then(function(details) {
                        // Send details to backend to mark payment as successful
                        fetch("{{ route('paypal.success', $data->id) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(details)
                        }).then(response => response.json())
                          .then(data => {
                              if (data.status === "success") {
                                  window.location.href = "{{ route('success.payment', $data->id) }}";
                              } else {
                                  window.location.href = "{{ route('declined.payment', $data->id) }}";
                              }
                          });
                    });
                },
                onCancel: function(data) {
                    window.location.href = "{{ route('paypal.cancel', $data->id) }}";
                },
                onError: function(err) {
                    alert("Something went wrong: " + err);
                }
            }).render('#paypal-button-container');
        };
        document.head.appendChild(script);
    });
</script>
@endpush
