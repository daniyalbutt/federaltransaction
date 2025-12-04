<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    <title>Payment Declined - {{ config('app.name', 'Laravel') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');
        .container {
            background: no-repeat;
            padding: 0px 15px;
            border-radius: 0;
            box-shadow: none;
        }
        .payment-wrapper {
            text-align: center;
            background-color: #f4f5ff;
            box-shadow: 0px 0px 20px 0px #00000040;
            border-radius: 40px;
            position: relative;
            padding: 80px 0px 40px;
        }
        a.close-icon {
            width: 80px;
            height: 80px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px !important;
            margin: 0 !important;
            position: absolute;
            left: 0;
            right: 0;
            margin: 0 auto !important;
            top: -45px;
            box-shadow: 0px 0px 0px 12px #f328286b;
        }
        .payment-declined a {
            background-color: #F32828;
            font-family: "Rubik", sans-serif !important;
            color: white !important;
            font-weight: 400 !important;
            font-size: 18px !important;
        }
        .payment-declined h1 {
            font-size: 60px;
            font-weight: bold;
            color: #F32828;
            margin: 0;
            margin-bottom: 15px;
            line-height: 58px;
            font-family: "Rubik", sans-serif;
        }
        .payment-declined h6 {
            font-size: 24px;
            font-weight: 400;
            color: black;
            margin-bottom: 10px;
        }
        h4, h5, h6 {
            font-family: "Rubik";
        }
        .payment-declined p {
            color: #7c7777;
            font-size: 19px;
            font-family: "Rubik", sans-serif;
            width: 63%;
            line-height: 28px;
            margin: 0 auto;
            margin-bottom: 15px;
        }
        a.close-icon img {
            max-width: 100%;
            height: auto;
        }
        section.thankyou-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <section class="thankyou-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="payment-wrapper payment-declined">
                        <a class="close-icon" href="#">
                        <img src="{{ asset('images/close-icon.png') }}">
                        </a>
                        <h1>Payment Declined</h1>
                        <h6>Your payment was declined</h6>
                        <p>{{ $transactionMessage }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
