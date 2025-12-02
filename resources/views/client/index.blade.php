@extends('layouts.front-app')
@section('content')
<div class="container-full">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title">Clients</h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="fa-solid fa-house"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Home</li>
                            <li class="breadcrumb-item active" aria-current="page">Clients</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <a class="btn btn-primary" href="{{ route('clients.create') }}">Add Client</a>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive rounded card-table">
                        <table class="table border-no" id="example1">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Join Date</th>
                                    <th>Customer Name</th>
                                    <th>Location</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover-primary">
                                    <td>#245879</td>
                                    <td>14 April 2021</td>
                                    <td>Aaliyah clark</td>
                                    <td>1623 E Updahl Ct, Harrison, ID, 83833</td>
                                    <td>$124.6</td>
                                    <td>$24.6</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245880</td>
                                    <td>25 April 2021</td>
                                    <td>Boone Doe</td>
                                    <td>261 Poplar Ave, Devon, PA, 19333</td>
                                    <td>$274.99</td>
                                    <td>$74.99</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245881</td>
                                    <td>25 April 2021</td>
                                    <td>Carlie Paton</td>
                                    <td>8959 State 405 Rte, Maceo, KY, 42355</td>
                                    <td>$616.21</td>
                                    <td>$66.21</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245882</td>
                                    <td>27 April 2021</td>
                                    <td>Delilah</td>
                                    <td>4480 Ka Haku Rd, Princeville, HI, 96722 </td>
                                    <td>$89.32</td>
                                    <td>$819.32</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245883</td>
                                    <td>27 April 2021</td>
                                    <td>Hannah Doe</td>
                                    <td>128 Mclemore Rd, Taft, TN, 38488</td>
                                    <td>$185.2</td>
                                    <td>$85.2</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245884</td>
                                    <td>27 April 2021</td>
                                    <td>Emerson Clark</td>
                                    <td>505 E 14th St, Scotland Neck, NC, 27874</td>
                                    <td>$48.5</td>
                                    <td>$18.5</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245885</td>
                                    <td>27 April 2021</td>
                                    <td>Crystal Doe</td>
                                    <td>312 S Judd St, Sioux City, IA, 51103</td>
                                    <td>$225.2</td>
                                    <td>$125.2</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245886</td>
                                    <td>29 April 2021</td>
                                    <td>Jenny don</td>
                                    <td>4381 Rutledge Pike, Rutledge, TN, 37861</td>
                                    <td>$329.25</td>
                                    <td>$39.25</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245887</td>
                                    <td>29 April 2021</td>
                                    <td>Joanne Clark</td>
                                    <td>Po Box 232, Bimble, KY, 40915</td>
                                    <td>$255.2</td>
                                    <td>$55.2</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245888</td>
                                    <td>30 April 2021</td>
                                    <td>Madeline doe</td>
                                    <td>146 Patterson Dr, Hayneville, AL, 36040</td>
                                    <td>$224.55</td>
                                    <td>$24.55</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-primary">
                                    <td>#245889</td>
                                    <td>30 April 2020</td>
                                    <td>Melinda</td>
                                    <td>143 Portsmouth Cir, Glen Mills, PA, 19342</td>
                                    <td>$278.5</td>
                                    <td>$78.5</td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">View Details</a>
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script type="text/javascript">
    	$(function () {
    		'use strict';
    		$('#example1').DataTable({
		  		'paging'      : true,
		  		'lengthChange': false,
		  		'searching'   : false,
		  		'ordering'    : true,
		  		'info'        : true,
		  		'autoWidth'   : false
			});
    	});
    </script>
@endpush