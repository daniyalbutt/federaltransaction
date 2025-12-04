@extends('layouts.payment-app')
@section('content')
    
    @if ($data->status == 0)
    <form id="card-form" action="{{ route('pay.now') }}" method="post">
        <input type="hidden" name="id" value="{{ $data->id }}">
        <input type="hidden" name="amount" value="{{ $data->price }}">
        @csrf
        <div class="container" style="height: 100vh;">
            <div id="error-message"></div>
            <div class="row h-100">
                <div class="col-md-6 pr-0">
                    <div class="payment-left">
                        <div id="invoiceImageContainer">

                        </div>
                        <button type="button" id="downloadInvoice" class="btn btn-info mt-3" style="display:none;">Download Invoice</button>
                    </div>
                </div>
                <div class="col-md-6 pl-0">
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
@endsection
@push('scripts')
    <script>
        $("#cardnumber")
            .on("keydown", function (e) {
                const cursor = this.selectionStart;

                if (this.selectionEnd !== cursor) return;

                // Handle delete
                if (e.which === 46 && this.value[cursor] === " ") {
                    this.selectionStart++;
                }
                // Handle backspace
                else if (e.which === 8 && cursor && this.value[cursor - 1] === " ") {
                    this.selectionEnd--;
                }
            })
            .on("input", function () {
                let value = this.value.replace(/[^0-9]/g, "").substring(0, 16);
                let formatted = "";

                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) formatted += " ";
                    formatted += value[i];
                }

                this.value = formatted;
            });
        
        var form = document.getElementById('card-form');

        function validateExpiry() {
            const month = parseInt($("#expiry").val(), 10);
            const year = parseInt($("#exp_year").val(), 10);

            if (month < 1 || month > 12) return false;
            if (year < 0 || year > 99) return false;

            return true;
        }

        function checkRequiredFields() {
            let error = false;

            $("#card-form")
                .find("input[required]")
                .each(function () {
                    if (!$(this).val().trim()) {
                        $(this).addClass("required");
                        error = true;
                    } else {
                        $(this).removeClass("required");
                    }
                });

            if (!validateExpiry()) error = true;

            return error;
        }
        
        $("#stripe-submit").click(function (e) {
            e.preventDefault();
            $("#stripe-submit").hide();
            $("#loader").show();
            if (checkRequiredFields()) {
                $(".error")
                    .removeClass("hide")
                    .find(".alert")
                    .text("Please fill all required fields correctly.");

                $("#loader").hide();
                $("#stripe-submit").show();
                return;
            }
            Stripe.setPublishableKey("{{ $data->merchants->public_key }}");
            Stripe.createToken(
                {
                    name: $("#cardname").val(),
                    number: $("#cardnumber").val(),
                    cvc: $("#cvv").val(),
                    exp_month: $("#expiry").val(),
                    exp_year: $("#exp_year").val(),
                    address_zip: $("#zip").val(),
                    address_country: $("#country").val(),
                    address_line1: $("#address").val(),
                    address_state: $("#state").val(),
                    address_city: $("#city").val(),
                },
                stripeResponseHandler
            );
        });
        
        function stripeResponseHandler(status, response) {
            if (response.error) {
                $(".error")
                    .removeClass("hide")
                    .find(".alert")
                    .text(response.error.message);

                $("#loader").hide();
                $("#stripe-submit").show();
                return;
            }

            // Success!
            const token = response.id;
            const form = document.getElementById("card-form");

            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "stripeToken";
            hidden.value = token;

            form.appendChild(hidden);
            form.submit();
        }

        function checkEmptyFileds() {
            var errorCount = 0;
            $('form#card-form').find('input[required]').each(function() {
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
@endpush