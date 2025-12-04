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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>

<body>
    <div id="invoice" style="position:absolute; left:-9999px; top:-9999px;">
        <div class="invoice">
            <div class="invoice-header">
                <img src="{{ asset($data->client->brand->image) }}" alt="{{ $data->client->brand->name }}">
                <div class="invoice-header-content">
                    <h2>BILLED: {{ $data->client->brand->name }}<br>{{ $data->client->brand->address_first_line }}<br>{{ $data->client->brand->address_second_line }}<br>{{ $data->client->brand->address_third_line }}</h2>
                    <p>This information is private and confidential it is intended solely for the named parties and may not be circulated publicly all rights reserved under UCC 1-308 written the United States.</p>
                </div>
            </div>

            <div class="invoice-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Fee Amount</th>
                            <th>Fee Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->items as $key => $value)
                        <tr>
                            <td><h6>{{ $value->name }}</h6><p>{{ $value->description }}</p></td>
                            <td>${{ $value->fee_amount }}</td>
                            <td>{{ $value->fee_code }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @php
                    $subTotal = collect($data->items)->sum('fee_amount');
                @endphp
                <div class="totals">
                    <div><span>Sub Total</span><span>${{ number_format($subTotal, 2) }}</span></div>
                    <div><span>Tax</span><span>${{ $data->tax_amount }}</span></div>
                    <div><strong>Total</strong><strong>${{$data->price}}</strong></div>
                </div>

                <div style="clear: both;"></div>

                <div class="terms">
                    <p>TERMS & CONDITIONS:<br>This information is private and confidential. No third parties are permitted access.</p>
                </div>
            </div>
        </div>
    </div>

    @if (Session::has('error'))
        <p class="alert alert-danger">{{ Session::get('error') }}</p>
    @endif
    @if (session('message'))
        <div class="success-alert alert alert-info">{{ session('message') }}</div>
    @endif

    @if (Session::has('stripe_error'))
        <p class="alert alert-danger">{{ Session::get('stripe_error') }}</p>
    @endif

    @yield('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"
        integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://js.stripe.com/v2/"></script>
    <script src="{{ asset('front/js/country-states.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        $(document).ready(function () {
            var raw_id = "{{ $data->id }}";
            var invoice_id = raw_id.toString();
            if (invoice_id.length < 4) {
                invoice_id = invoice_id.padStart(4, '0');
            }
            var invoice = document.getElementById('invoice');
            html2canvas(invoice, { scale: 2, useCORS: true }).then(function (canvas) {
                var imgData = canvas.toDataURL('image/png');
                $('#invoiceImageContainer').html(
                    '<img id="invoiceRenderedImage" src="' + imgData + '" style="max-width:100%;">'
                );
                $('#downloadInvoice').show();
                $('#downloadInvoice').on('click', function () {
                    var link = document.createElement('a');
                    link.href = imgData;
                    link.download = "invoice-" + invoice_id + ".png";
                    link.click();
                });
            });
        });
    </script>
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
    @stack('scripts')
    </body>
</html>