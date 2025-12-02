<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        .StripeElement {
            box-sizing: border-box;
            height: 45px;
            padding: 12px 12px;
            border: 1px solid transparent;
            border-radius: 8px;
            background-color: white;
            transition: box-shadow 150ms ease;
            border-width: 1px;
            border-color: lightgrey;
            border-style: solid;
            margin-bottom: 10px;
            margin-top: 3px;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
        /*NEW CSS*/
        *{
            font-family: "Oswald", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }
        .payment-right {
            padding: 40px;
            box-shadow: -2px -2px 2px 0 #e5e5e5;
            background-color: white;
            height: 100%;
            display: flex;
            align-items: center;
        }
        input, textarea, select {
            border-radius: 8px !important;
            font-size: 15px !important;
            height: 42px !important;
            padding-left: 15px !important;
        }
        label {
            font-weight: 400;
            font-size: 14px;
            color: #0000008a;
        }
        select#state{
            margin: 0;
            border-top-left-radius: 0px !important;
            border-bottom-left-radius: 0px !important;
            border-top-right-radius: 0 !important;
        }
        .payment-left {
            display: flex;
            align-items: center;
            height: 100%;
            background-color: white;
            padding-left: 40px;
        }
        
        .payment-left-inner h3 {
            font-weight: bold;
            font-size: 25px;
        }
        .payment-left-inner h2 {
            font-weight: bold;
            font-size: 40px;
            margin: 0;
            color: #17a2b8;
        }
        .payment-left-inner img {
            margin-bottom: 20px;
        }
        .payment-left-inner h1 {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-size: 32px;
        }
        input#cardnumber {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0px !important;
            margin-bottom: 0;
            border-top-right-radius: 0px !important;
            margin-top: 0;
            border-right: 0;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        
        input#expiry {
            margin: 0;
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        input#cvv {
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0px !important;
            margin-top: 0;
            border-bottom-left-radius: 0 !important;
        }
        input#exp_year {
            margin-top: 0;
            border-radius: 0px !important;
        }
        .form-control::placeholder {
            color: #d1d1d1;
            opacity: 1; /* Firefox */
        }
        
        .form-control::-ms-input-placeholder { /* Edge 12 -18 */
            color: #d1d1d1;
        }
        
        .error.hide {
            display: none;
        }
        
        .form-control.required {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .form-control.required::placeholder {
            color: #721c24;
            opacity: 1; /* Firefox */
        }
        
        .form-control.required::-ms-input-placeholder { /* Edge 12 -18 */
            color: #721c24;
        }
        
        span#basic-addon2 {
            background-color: white;
            padding: 0;
            border-bottom-right-radius: 0;
            border-left: 0;
            padding-right: 10px;
        }
        
        .input-group-append {
            margin: 0;
        }
    </style>
</head>

<body>
    @if (Session::has('error'))
        <p class="alert alert-danger">{{ Session::get('error') }}</p>
    @endif
    @if (session('message'))
        <div class="success-alert alert alert-info">{{ session('message') }}</div>
    @endif

    @if (Session::has('stripe_error'))
        <p class="alert alert-danger">{{ Session::get('stripe_error') }}</p>
    @endif
    
    @if ($data->status == 0)
    <form id="card-form" action="{{ route('pay.now') }}" method="post">
        <input type="hidden" name="id" value="{{ $data->id }}">
        <input type="hidden" name="amount" value="{{ $data->price }}">
        @csrf
        <div class="container" style="height: 100vh;">
            <div id="error-message"></div>
            <div class="row h-100">
                <div class="col-md-5 pr-0">
                    <div class="payment-left">
                        <div class="payment-left-inner">
                            <h1>{{ $data->client->brand->name }}</h1>
                            <img src="{{ asset($data->client->brand->image) }}" width="180"/>
                            <!--<h3>{{ $data->package }}</h3>-->
                            <h2>${{ $data->price }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 pl-0">
                    <div class="payment-right">
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <label for="user_name">Name</label>
                                <input id="user_name" name="user_name" class="form-control" type="text" value="{{ $data->client->name }}">
                            </div>
                            <div class="col-md-6 mb-1">
                                <label for="user_email">Email Address</label>
                                <input id="user_email" name="user_email" class="form-control" type="email" value="{{ $data->client->email }}">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="card_information">Card Information</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <input type="text" id="cardnumber" name="cardnumber" placeholder="0000-0000-0000-0000" class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon2">
                                                    <img src="{{ asset('images/payment-img.png') }}">
                                                </span>
                                            </div>
                                        </div>
                                        <!--<div id="card"></div>-->
                                        <!--<div id="card-errors" role="alert"></div>-->
                                    </div>
                                    <div class="col-md-4 pr-0">
                                        <input id="expiry" name="exp_month" type="text" placeholder="MM" maxlength="2" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 pr-0 pl-0">
                                        <input id="exp_year" name="exp_year" type="text" placeholder="YY" maxlength="2" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <input type="text" id="cvv" name="cvv" placeholder="CVV" maxlength="4" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="owner">Name on card</label>
                                <input type="text" id="cardname" name="owner" class="form-control" placeholder="{{ $data->client->name }}" required>
                            </div>
                            <div class="col-md-12">
                                <label for="country">Country or region</label>
                                <div class="row no-gap-row">
                                    <div class="col-md-6 pr-0">
                                        <select name="country" id="country" class="form-control" required style="border-top-right-radius: 0 !important;border-bottom-right-radius: 0px !important;border-bottom-left-radius: 0 !important;margin-bottom: 0;">
                                            <option>Select Country *</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-0">
                                        <input name="city" id="city" required class="form-control" placeholder="City*" value="" style="border-radius: 0 !important;margin-bottom: 0;">
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <input name="zip" id="zip" class="form-control" placeholder="ZIP*" required style="border-top-left-radius: 0 !important;border-bottom-right-radius: 0px !important;border-bottom-left-radius: 0 !important;margin-bottom: 0;">
                                    </div>
                                    <div class="col-md-8 pr-0">
                                        <input name="address" id="address" class="form-control" placeholder="Address*" value="" required style="margin: 0;border-top-left-radius: 0 !important;border-top-right-radius: 0px !important;border-bottom-right-radius: 0px !important;">
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <span id="state-code"><input type="text" id="state" class="form-control" placeholder="State*" name="state" value=""></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="error hide">
                                    <p class="alert alert-danger"></p>
                                </div>
                                <button class="btn btn-info pl-5 pr-5 form-submit-btn" id="stripe-submit">Pay Now</button>
                                <div id="loader" style="display: none;">
                                    <img src="{{ asset('images/loader.gif') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
        @if ($data->status == 2)
            <div class="success-alert alert alert-info">PAID!</div>
        @elseif($data->status == 1)
            <div class="success-alert alert alert-info">{{ $data->return_response }}</div>
        @endif
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"
        integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://js.stripe.com/v2/"></script>
    <script src="{{ asset('front/js/country-states.js') }}"></script>
    <script>
        // user country code for selected option
        let user_country_code = "US";
    
        (function () {
    
            // Get the country name and state name from the imported script.
            let country_list = country_and_states['country'];
            let states_list = country_and_states['states'];
    
            // creating country name drop-down
            let option =  '';
            option += '<option>select country</option>';
            for(let country_code in country_list){
                // set selected option user country
                let selected = (country_code == user_country_code) ? ' selected' : '';
                option += '<option value="'+country_code+'"'+selected+'>'+country_list[country_code]+'</option>';
            }
            document.getElementById('country').innerHTML = option;
    
            // creating states name drop-down
            let text_box = '<input type="text" class="input-text" id="state">';
            let state_code_id = document.getElementById("state-code");
    
            function create_states_dropdown() {
                // get selected country code
                let country_code = document.getElementById("country").value;
                let states = states_list[country_code];
                // invalid country code or no states add textbox
                if(!states){
                    state_code_id.innerHTML = text_box;
                    return;
                }
                let option = '';
                if (states.length > 0) {
                    option = '<select id="state" name="set_state">\n';
                    for (let i = 0; i < states.length; i++) {
                        option += '<option value="'+states[i].code+'">'+states[i].name+'</option>';
                    }
                    option += '</select>';
                } else {
                    // create input textbox if no states 
                    option = text_box
                }
                state_code_id.innerHTML = option;
            }
    
            // country select change event
            const country_select = document.getElementById("country");
            country_select.addEventListener('change', create_states_dropdown);
    
            create_states_dropdown();
        })();
    </script>
    <script>
        $("#cardnumber").on("keydown", function(e) {
            var cursor = this.selectionStart;
            if (this.selectionEnd != cursor) return;
            if (e.which == 46) {
                if (this.value[cursor] == " ") this.selectionStart++;
            } else if (e.which == 8) {
                if (cursor && this.value[cursor - 1] == " ") this.selectionEnd--;
            }
        }).on("input", function() {
            var value = this.value;
            var cursor = this.selectionStart;
            var matches = value.substring(0, cursor).match(/[^0-9]/g);
            if (matches) cursor -= matches.length;
            value = value.replace(/[^0-9]/g, "").substring(0, 19);
            var formatted = "";
            for (var i=0, n=value.length; i<n; i++) {
                if (i && i % 4 == 0) {
                    if (formatted.length <= cursor) cursor++;
                    formatted += " ";
                }
                formatted += value[i];
            }
            if (formatted == this.value) return;
            this.value = formatted;
            this.selectionEnd = cursor;
        });
        
        var form = document.getElementById('card-form');

        $('#stripe-submit').click(function(e) {
            e.preventDefault();
            $('#stripe-submit').hide();
            $('#loader').show();
            
            Stripe.setPublishableKey('{{ $data->merchants->public_key }}');
            Stripe.createToken({
                name: $('#cardname').val(),
                number: $('#cardnumber').val(),
                cvc: $('#cvv').val(),
                exp_month: $('#expiry').val(),
                exp_year: $('#exp_year').val(),
                address_zip: $('#zip').val(),
                address_country: $('#country').val(),
                address_line1: $('#address').val(),
                address_state: $('#state').val(),
                address_city: $('#city').val()
            }, stripeResponseHandler);
                
            // stripe.createToken({
            //     name: $('#cardname').val(),
            //     number: $('#cardnumber').val(),
            //     cvc: $('#cvv').val(),
            //     exp_month: $('#expiry').val(),
            //     exp_year: $('#exp_year').val(),
            //     address_zip: $('#zip').val(),
            //     address_country: $('#country').val(),
            //     address_line1: $('#address').val(),
            //     address_state: $('#state').val(),
            //     address_city: $('#city').val()
            // }).then(function(result) {
            //     console.log(result);
            //     return false;
            //     var errorCount = checkEmptyFileds();
            //     if ((result.error) || (errorCount == 1)) {
            //         // Inform the user if there was an error.
            //         if (result.error) {
            //             var errorElement = document.getElementById('card-errors');
            //             $(errorElement).show();
            //             errorElement.textContent = result.error.message;
            //         } else {
            //             $.toast({
            //                 heading: 'Alert!',
            //                 position: 'bottom-right',
            //                 text: 'Please fill the required fields before proceeding to pay',
            //                 loaderBg: '#ff6849',
            //                 icon: 'error',
            //                 hideAfter: 5000,
            //                 stack: 6
            //             });
            //         }
            //         $('#loader').hide();
            //         $('#stripe-submit').show();
            //     } else {
            //         // Send the token to your server.
            //         stripeTokenHandler(result.token);
            //     }
            // });
        });
        
        function stripeResponseHandler(status, response) {
            
            if (response.error) {
                $('.error').removeClass('hide').find('.alert') .text(response.error.message);
                $('#loader').hide();
                $('#stripe-submit').show();
            } else {
                var errorCount = checkEmptyFileds();
                if (errorCount == 1){
                    $('.error').removeClass('hide').find('.alert') .text('Please fill the required fields before proceeding to pay');
                    $('#loader').hide();
                    $('#stripe-submit').show();
                }else{
                    var token = response['id'];
                    var form = document.getElementById('card-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token);
                    form.appendChild(hiddenInput);
                    form.submit();
                }
            }
        }

        function checkEmptyFileds() {
            var errorCount = 0;
            $('form#card-form').find('input').each(function() {
                if ($(this).prop('required')) {
                    if (!$(this).val()) {
                        $(this).addClass('required');
                        errorCount = 1;
                    }else{
                        $(this).removeClass('required');
                    }
                }
            });
            return errorCount;
        }


        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('card-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>
</body>

</html>
