<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>

    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

   
    <div class="page-content container">
        <div class="page-header text-blue-d2">
            <h1 class="page-title text-secondary-d1">
                Invoice
                <small class="page-info">
                    <i class="fa fa-angle-double-right text-80"></i>
                    ID: #{{ $data->unique_id }}
                </small>
            </h1>
    
         
        </div>
    
        <div class="container px-0">
            <div class="row mt-4">
                <div class="col-12 col-lg-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center text-150">
                                {{ strtoupper($data->client->brand_name) }}
                            </div>
                        </div>
                    </div>
                    <!-- .row -->
    
                    <hr class="row brc-default-l1 mx-n1 mb-4" />
    
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <span class="text-sm text-grey-m2 align-middle">To:</span>
                                <span class="text-600 text-110 text-blue align-middle">{{ ucwords($data->client->name) }}</span>
                            </div>
                            <div class="text-grey-m2">
                                <div class="my-1">
                                   <a href="mailto:{{ $data->client->email }}">{{ $data->client->email }}</a>
                                </div>
                                <div class="my-1">
                                    @if ($data->status != 0)
                                        {{ json_decode($data->payment_data)->ssl_avs_address}}
                                    @endif
                                </div>
                                <div class="my-1"><i class="fa fa-phone fa-flip-horizontal text-secondary"></i> <b class="text-600">{{ $data->client->phone }}</b></div>
                            </div>
                        </div>
                        <!-- /.col -->
    
                        <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                            <hr class="d-sm-none" />
                            <div class="text-grey-m2">
                                <div class="mt-1 mb-2 text-secondary-m1 text-600 text-125">
                                    Invoice
                                </div>
    
                                <div class="my-2"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">ID:</span> #{{ $data->unique_id }}</div>
    
                                <div class="my-2"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Issue Date:</span> Oct 12, 2019</div>
    
                                <div class="my-2"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">Status: </span><span class="badge {{ $data->get_badge_invoice_status() }}">{{ $data->get_status() }}</span></div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
    
                    <div class="mt-4">
                        <div class="row text-600 text-white bgc-default-tp1 py-25">
                            <div class="d-none d-sm-block col-1">#</div>
                            <div class="col-9 col-sm-5">Description</div>
                            <div class="d-none d-sm-block col-4 col-sm-2">Package Name</div>
                            <div class="d-none d-sm-block col-sm-2">Payment Type</div>
                            <div class="col-2">Amount</div>
                        </div>
    
                        <div class="text-95 text-secondary-d3">
                            <div class="row mb-2 mb-sm-0 py-25">
                                <div class="d-none d-sm-block col-1">1</div>
                                <div class="col-9 col-sm-5">{{ $data->package }} </div>
                                <div class="d-none d-sm-block col-2">{{ $data->description }}</div>
                                <div class="d-none d-sm-block col-2 text-95">One Time Charge</div>
                                <div class="col-2 text-secondary-d2">${{ $data->price }}</div>
                            </div>
    
                        </div>
    
                        <div class="row border-b-2 brc-default-l2"></div>
    
                        <!-- or use a table instead -->
                        <!--
                <div class="table-responsive">
                    <table class="table table-striped table-borderless border-0 border-b-2 brc-default-l1">
                        <thead class="bg-none bgc-default-tp1">
                            <tr class="text-white">
                                <th class="opacity-2">#</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th width="140">Amount</th>
                            </tr>
                        </thead>
    
                        <tbody class="text-95 text-secondary-d3">
                            <tr></tr>
                            <tr>
                                <td>1</td>
                                <td>Domain registration</td>
                                <td>2</td>
                                <td class="text-95">$10</td>
                                <td class="text-secondary-d2">$20</td>
                            </tr> 
                        </tbody>
                    </table>
                </div>
                -->
    
                        <div class="row mt-3">
                            <hr>
                            <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                {{ $data->package }}
                            </div>
    
                            <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                <div class="row my-2">
                                    <div class="col-7 text-right">
                                        Subtotal
                                    </div>
                                    <div class="col-5">
                                        <span class="text-120 text-secondary-d1">$2,250</span>
                                    </div>
                                </div>
    
                                <hr class="hr-bold">
                                <div class="row my-2 align-items-center bgc-primary-l3">
                                    <div class="col-7 text-right">
                                      <span class="text-150">Total Amount:</span>  
                                    </div>
                                  
                                    <div class="col-5">
                                        <span class="text-150 text-success-d3 opacity-2">$2,475</span>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <hr />
    
                    
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>