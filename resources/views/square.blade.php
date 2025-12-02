<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <style>
            .accept-checkout input {
                height: 20px !important;
                width: 20px !important;
                margin-right: 10px;
                position: relative;
                top: 5px;
            }

            .form-check.accept-checkout {
                display: flex;
                align-items: baseline;
                margin-bottom: 15px;
            }

            .accept-checkout label {
                font-size: 14px;
            }
            
            div#loader {
                text-align: center;
                display: none;
            }
        </style>
    </head>
    <body>
        @if(Session::has('error'))
        <p class="alert alert-danger">{{ Session::get('error') }}</p>
        @endif
        @if (session('message'))
        <div class="success-alert alert alert-info">{{ session('message') }}</div>
        @endif
        @if($data->status == 0)
        <div class="container">
            <div class="text-center">
                @php
                    $brand_name = $data->client->brand_name;
                @endphp
                  <img src="{{ asset('brands/'.$brand_name.'.png') }}">
            </div>
            <header>
                Customer info <span>${{ $data->price }}</span><br>
            </header>
            <div id="error-message"></div>
            <form id="card-form" action="{{ route('process.payment') }}" method="post">
                <input type="hidden" name="token" id="token" value="">
                <input type="hidden" name="idempotencyKey" id="idempotencyKey" value="">
                <input type="hidden" name="id" value="{{ $data->id }}">
                <input type="hidden" name="amount" value="{{ $data->price }}">
                @csrf
                <div class="field-row">
                    <input type="text" placeholder="Enter First Name" name="ssl_first_name" class="demoInputBox required" required><br>
                    <input type="text" placeholder="Enter Last Name" name="ssl_last_name" class="demoInputBox required" required><br>
                    <input type="email" placeholder="Enter Email" name="ssl_email" class="demoInputBox required" required><br>
                    <input type="text" name="ssl_avs_address" placeholder="Street Address" class="required" required><br>
                </div>
                <header>Credit Card Info</header>
                <br>
                <input type="hidden" name="id" value="{{ $data->id }}">

                <div id="card-container"></div>

                <span id="payment-flow-message"></span>
                <div>
                    <div class="form-check accept-checkout">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">I accept the terms of use and privacy policy</label>
                    </div>
                    <input disabled type="button" value="Submit" id="card-button" class="btn btn-primary btnAction button-credit-card p-0">
                    <div id="loader">
                      <img alt="loader" src="{{ asset('images/loader.gif') }}">
                    </div>
                </div>
            </form>
        </div>
        @else
        <div class="success-alert alert alert-info">{!! $data->return_response !!}</div>
        @endif
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @if(env('SQUARE_ENVIRONMENT') == 'sandbox')
        <script src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
        @else
        <script src="https://web.squarecdn.com/v1/square.js"></script>
        @endif
        <script>
        async function CardPay(fieldEl, buttonEl) {
            // Create a card payment object and attach to page
            const card = await window.payments.card({
                style: {
                    '.input-container.is-focus': {
                        borderColor: '#006AFF'
                    },
                    '.message-text.is-error': {
                        color: '#BF0020'
                    }
                }
            });
            await card.attach(fieldEl);

            async function eventHandler(event) {
                $('#card-button').hide();
                $('#loader').show();
                var error = 0;
                $('.field-row input').each(function(){
                  if($(this).hasClass('required')){
                    if($(this).val() == ''){
                      $(this).addClass('error');
                      error = 1;
                    }else{
                      $(this).removeClass('error');
                    }
                  }
                })
                // Clear any existing messages
                window.paymentFlowMessageEl.innerText = '';
                try {
                    const result = await card.tokenize();
                    if (result.status === 'OK') {
                        if(error == 0){
                          window.createPayment(result.token);
                        }else{
                          $('#payment-flow-message').addClass('alert alert-danger d-block');
                          $('#payment-flow-message').text('Error: Please Fill Required Fields');
                          $('#card-button').show();
                          $('#loader').hide();
                        }
                    }else{
                        $('#card-button').show();
                        $('#loader').hide();
                    }
                } catch (e) {
                    if (e.message) {
                        $('#card-button').show();
                        $('#loader').hide();
                        window.showError(`Error: ${e.message}`);
                    } else {
                        window.showError('Something went wrong');
                    }
                }
            }

            buttonEl.addEventListener('click', eventHandler);
        }

        async function SquarePaymentFlow() {
            // Create card payment object and attach to page
            CardPay(document.getElementById('card-container'), document.getElementById('card-button'));
        }

        var getPaymentMethod = {!! json_encode($data->payment_method) !!};
        
        window.payments = Square.payments("{{ env('SQUARE_APPLICATION_ID') }}", "{{ env('SQUARE_LOCATION_ID') }}");

        window.paymentFlowMessageEl = document.getElementById('payment-flow-message');

        window.showSuccess = function(message) {
            window.paymentFlowMessageEl.classList.add('success');
            window.paymentFlowMessageEl.classList.remove('error');
            window.paymentFlowMessageEl.innerText = message;
        }

        window.showError = function(message) {
            window.paymentFlowMessageEl.classList.add('error');
            window.paymentFlowMessageEl.classList.remove('success');
            window.paymentFlowMessageEl.innerText = message;
        }

        window.createPayment = async function(token) {
            $('#token').val(token);
            $('#idempotencyKey').val(window.idempotencyKey);
            $('#card-form').submit();
            
            const dataJsonString = JSON.stringify({
                token,
                idempotencyKey: window.idempotencyKey,
                name: $('input[name="ssl_first_name"]').val() + ' ' + $('input[name="ssl_last_name"]').val(),
                email: $('input[name="ssl_email"]').val(),
                address: $('input[name="ssl_avs_address"]').val(),
                id: "{{ $data->unique_id }}"
            });
            
            console.log(token);

            // try {
            //     const response = await fetch('{{ route("process.payment") }}', {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         body: dataJsonString
            //     });
            //     console.log(response);
            //     const data = await response.json();

            //     location.reload();

            //     if(data.status == 'success'){
            //         window.showSuccess('Payment Successful!');
            //     }else{
            //         window.showError(data.data);
            //     }

            // } catch (error) {
            //     console.log(error);
            //     console.error('Error:', error);
            // }
        }
        SquarePaymentFlow();
        </script>
        <script>
            $('#exampleCheck1').change(function(){
                if(this.checked) {
                    $('#card-button').prop('disabled', false);
                }else{
                    $('#card-button').prop('disabled', true);
                }
            })
        </script>
    </body>
</html>
