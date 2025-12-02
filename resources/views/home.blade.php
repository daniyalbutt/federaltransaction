@extends('layouts.front-app')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        @can('payment')
        <div class="col-xl-9 col-xxl-9">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card bg-blue action-card h-auto">
                        <form action="{{ route('home') }}" class="monthly-form">
                            <div class="d-flex mb-3">
                                <select name="month" id="month" class="form-control me-2">
                                    <option value="1" {{ $month == 1 ? 'selected' : '' }}>January</option>
                                    <option value="2" {{ $month == 2 ? 'selected' : '' }}>February</option>
                                    <option value="3" {{ $month == 3 ? 'selected' : ''}} {{ request()->month == 3 ? 'selected' : '' }}>March</option>
                                    <option value="4" {{ $month == 4 ? 'selected' : ''}}>April</option>
                                    <option value="5" {{ $month == 5 ? 'selected' : ''}}>May</option>
                                    <option value="6" {{ $month == 6 ? 'selected' : ''}}>June</option>
                                    <option value="7" {{ $month == 7 ? 'selected' : ''}}>July</option>
                                    <option value="8" {{ $month == 8 ? 'selected' : ''}}>August</option>
                                    <option value="9" {{ $month == 9 ? 'selected' : ''}}>September</option>
                                    <option value="10" {{ $month == 10 ? 'selected' : ''}}>October</option>
                                    <option value="11" {{ $month == 11 ? 'selected' : ''}}>November</option>
                                    <option value="12" {{ $month == 12 ? 'selected' : ''}}>December</option>
                                </select>
                                <select name="year" id="year" class="form-control ml-2">
                                    @for($i = 2024; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : ''}}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </form>
                        <div class="card-header flex-wrap border-0 pb-0 align-items-end pt-0">
                            <div class="mb-3 me-3">
                                <h5 class="fs-20 text-white font-w500">Monthly Payment - {{ date('F') }}</h5>
                                <span class="text-num text-white fs-30 font-w500">
                                    ${{ $month_paid }}
                                </span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1"></p>
                                <span class="text-white fs-15">
                                    Last ID #{{ $last != null ? $last->id : '' }} - ${{ $last != null ? $last->price : '' }}
                                </span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1 text-white">Declined</p>
                                <span class="text-white fs-15">{{ $total_declined }}</span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1 text-white">Completed</p>
                                <span class="text-white fs-15">{{ $total_completed }}</span>
                            </div>
                            <span class="fs-18 text-white font-w500 me-3 mb-3"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                            <div class="mb-3 me-3">
                                <h5 class="fs-20 text-black font-w500">Last Payment</h5>
                                <span class="text-num text-black fs-30 font-w500">
                                    {{ $last_payment != null ? $last_payment->price : '0' }}
                                </span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1">VALID THRU</p>
                                <span class="text-black fs-15">
                                    {{ $last_payment != null ? date('m/Y', strtotime($last_payment->updated_at)) : '0' }}
                                </span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1">CARD HOLDER</p>
                                <span class="text-black fs-15">{{ $last_payment != null ? $last_payment->client->name : '' }}</span>
                            </div>
                            <span class="fs-18 text-black font-w500 me-3 mb-3">{{ $last_payment != null ? $last_payment->getCard() : ' ' }}</span>
                        </div>
                        <div class="card-body">
                            <div class="progress default-progress">
                                <div class="progress-bar bg-gradient-5 progress-animated" style="width: 50%; height:20px;" role="progressbar">
                                    <span class="sr-only">50% Complete</span>
                                </div>
                            </div>
                            <div class="row mt-4 pt-3">
                                <div class="col-xl-6 col-xxl-5 col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-6 col-7">
                                            <h4 class="card-title">Monthly Summary</h4>
                                            <ul class="card-list mt-3">
                                                <li class="mb-2"><span class="bg-success circle"></span><span class="ms-0">Completed</span><span class="text-black fs-18">{{$completed_percentage}}%</span></li>
                                                <li class="mb-2"><span class="bg-light circle"></span><span class="ms-0">Pending</span><span class="text-black fs-18">{{$pending_percentage}}%</span></li>
                                                <li class="mb-2"><span class="bg-danger circle"></span><span class="ms-0">Declined</span><span class="text-black fs-18">{{$declined_percentage}}%</span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-6 col-5">
                                            <canvas id="pieChart" data-one="{{$completed_percentage}}" data-two="{{$pending_percentage}}" data-three="{{$declined_percentage}}"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-xxl-7 col-lg-6">
                                    <div id="line-chart" class="bar-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex border-0">
                            <div class="me-3">
                                <h4 class="card-title mb-2">Payment History</h4>
                            </div>
                        </div>
                        <div class="card-body tab-content p-0">
                            <div class="tab-pane fade active show" id="monthly" role="tabpanel">
                                <div id="accordion-one" class="accordion style-1">
                                    @foreach($data as $key => $value)
                                    <div class="accordion-item">
                                        <div class="accordion-header {{ $value->status != 2 ? 'collapsed' : '' }}" data-bs-toggle="collapse" data-bs-target="#default_collapseOne{{$value->id}}">
                                            <div class="d-flex align-items-center">
                                                <div class="user-info">
                                                    <h6 class="fs-14 font-w700 mb-0"><a href="javascript:void(0)">{{ $value->client->name }}</a></h6>
                                                    <span class="fs-14">{{ $value->client->email }}</span>
                                                </div>
                                            </div>
                                            <span>{{ $value->created_at->format('d M, Y') }} <br> {{ $value->created_at->format('g:i A') }}</span>
                                            <span>${{ $value->price }} - {{ $value->merchants != null ? $value->merchants->getMerchant() : '' }}<br>{{ $value->merchants != null ? $value->merchants->name : '' }} - {{ Illuminate\Support\Str::limit($value->client->brand->name, 20) }}</span>
                                            <span class="badge badge-primary badge-sm" onclick="withJquery('{{ route('pay', [$value->unique_id]) }}')" style="cursor: pointer;">COPY LINK</span>
                                            <a class="btn {{ $value->get_badge_status() }} btn-sm light" href="javascript:void(0);">{{ $value->get_status() }}</a>
                                            <span class="accordion-header-indicator"></span>
                                        </div>
                                        <div id="default_collapseOne{{$value->id}}" class="collapse accordion_body {{ $value->status != 2 ? '' : 'show' }}" data-bs-parent="#accordion-one">
                                            <div class="payment-details accordion-body-text">
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">ID Payment</p>
                                                    <span class="font-w500">#{{ $value->id }}</span>
                                                </div>
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">Payment Method</p>
                                                    <span class="font-w500">
                                                        @if($value->status == 2)
                                                        {{ $value->return_response != null ? $value->getCardBrand() : '' }}
                                                        @else
                                                        NONE
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">Invoice Date</p>
                                                    <span class="font-w500">{{ $value->created_at->format('d M, Y g:i A') }}</span>
                                                </div>
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">Date Paid</p>
                                                    <span class="font-w500">{{ $value->updated_at->format('d M, Y g:i A') }}</span>
                                                </div>
                                                <div class="info mb-3">
                                                    <svg class="me-3" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 1C9.82441 1 7.69767 1.64514 5.88873 2.85384C4.07979 4.06253 2.66989 5.7805 1.83733 7.79049C1.00477 9.80047 0.786929 12.0122 1.21137 14.146C1.6358 16.2798 2.68345 18.2398 4.22183 19.7782C5.76021 21.3166 7.72023 22.3642 9.85401 22.7887C11.9878 23.2131 14.1995 22.9953 16.2095 22.1627C18.2195 21.3301 19.9375 19.9202 21.1462 18.1113C22.3549 16.3023 23 14.1756 23 12C22.9966 9.08368 21.8365 6.28778 19.7744 4.22563C17.7122 2.16347 14.9163 1.00344 12 1ZM12 21C10.22 21 8.47992 20.4722 6.99987 19.4832C5.51983 18.4943 4.36628 17.0887 3.68509 15.4442C3.0039 13.7996 2.82567 11.99 3.17294 10.2442C3.5202 8.49836 4.37737 6.89471 5.63604 5.63604C6.89472 4.37737 8.49836 3.5202 10.2442 3.17293C11.99 2.82567 13.7996 3.0039 15.4442 3.68509C17.0887 4.36627 18.4943 5.51983 19.4832 6.99987C20.4722 8.47991 21 10.22 21 12C20.9971 14.3861 20.0479 16.6736 18.3608 18.3608C16.6736 20.048 14.3861 20.9971 12 21Z" fill="#fff"/>
                                                        <path d="M12 9C11.7348 9 11.4804 9.10536 11.2929 9.29289C11.1054 9.48043 11 9.73478 11 10V17C11 17.2652 11.1054 17.5196 11.2929 17.7071C11.4804 17.8946 11.7348 18 12 18C12.2652 18 12.5196 17.8946 12.7071 17.7071C12.8947 17.5196 13 17.2652 13 17V10C13 9.73478 12.8947 9.48043 12.7071 9.29289C12.5196 9.10536 12.2652 9 12 9Z" fill="#fff"/>
                                                        <path d="M12 8C12.5523 8 13 7.55228 13 7C13 6.44771 12.5523 6 12 6C11.4477 6 11 6.44771 11 7C11 7.55228 11.4477 8 12 8Z" fill="#fff"/>
                                                    </svg>
                                                    <p class="mb-0 fs-14">{{ $value->package }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3">
            <div class="row">
                <div class="col-md-12 col-sm-6">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex border-0">
                            <div>
                                <h4 class="card-title mb-2">Customer List</h4>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @foreach($customer as $key => $value)
                            <div class="invoice-list">
                                <div class="me-auto">
                                    <h6 class="fs-15 font-w600 mb-0"><a href="javascript:;" class="text-black">{{ $value->name }}</a></h6>
                                </div>
                                <span class="fs-15 text-black font-w600">${{ $value->get_total_amount() }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
@push('scripts')
<script>
    function withJquery(link){
	    var temp = $("<input>");
        $("body").append(temp);
        temp.val(link).select();
        document.execCommand("copy");
        temp.remove();
        console.timeEnd('time1');
    }

    var data = {!! json_encode($graph_data->toArray()) !!};

    var data_array = [];
    var data_day = [];
    for (var i = 0; i < data.length; i++) {
        data_array.push(data[i].price);
        data_day.push(data[i].invoice_date);
    }

    console.log(data_day);

    var lineChart = function(){
		var options = {
		  	series: [{
				name: "Completed",
				data: data_array
			}],
		  	chart: {
		  		height: 170,
		  		type: 'line',
		  		toolbar:{
					show:false
		  		},
		  		zoom: {
					enabled: false
		  		}
			},
			colors:['#68e365'],
			dataLabels: {
		  		enabled: false
			},
			stroke: {
		  		curve: 'smooth',
		  		width:3
			},
			legend:{
				show:false
			},
			grid: {
				xaxis: {
					lines: {
						show: true
					}
				},
			},
			xaxis: {
		  		categories: data_day,
			},
			yaxis:{
				show:false
			}
		};
		
		var chart = new ApexCharts(document.querySelector("#line-chart"), options);
		chart.render();
	}

    $('.monthly-form select').change(function(){
        $('.monthly-form').submit();
    });
</script>
@endpush