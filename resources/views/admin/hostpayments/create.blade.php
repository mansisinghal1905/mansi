
@extends('admin.layouts.backend.app')

@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Host Payment</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.hostpayments.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.hostpayments.index') }}" class="btn btn-outline-primary d-flex align-items-center">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                    </div>
             
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-top-0">
                           
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
                                    <div class="card-body personal-info">

                                    <form action="{{ $payment ? route('admin.hostpayments.update', $payment->id) : route('admin.hostpayments.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($payment)
                                            @method('PUT')
                                        @endif
                                        @if (session('status'))
                                            <div class="alert alert-success" role="alert">
                                                {{ session('status') }}
                                            </div>
                                        @elseif (session('error'))
                                            <div class="alert alert-danger" role="alert">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        <div class="mb-4 d-flex align-items-center justify-content-between">
                                            <h5 class="fw-bold mb-0 me-4">
                                                <span class="d-block mb-2">Host Customer Payment Information:</span>
                                                <!-- <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span> -->
                                            </h5>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Customer: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control @error('host_customer_id') is-invalid @enderror" data-select2-selector="tag" name="host_customer_id" id="host_customer_id">
                                                    @if(count($customerlist) > 0)
                                                        <option value="">Select Customer</option>
                                                        @foreach($customerlist as $client)
                                                            <option value="{{ $client->id }}" @if(isset($invoicepayment) && $invoicepayment->host_customer_id == $client->id) selected @endif>{{ ucfirst($client->name) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No Customer found</option>
                                                    @endif
                                                </select>
                                                @error('host_customer_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                       

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="titleInput" class="fw-semibold">Amount ($): </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                   
                                                    <input type="number" class="form-control @error('total_amount') is-invalid @enderror" name="total_amount" value="{{ isset($payment->total_amount) ? $payment->total_amount : old('total_amount') }}" id="titleInput" placeholder=" Amount">
                                                </div>
                                                @error('total_amount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="titleInput" class="fw-semibold">Purpose Of Payment: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <textarea class="form-control" id="" name="description"  id="addressInput_2" cols="30" rows="3" placeholder="Purpose Of Payment">{{ isset($payment->description) && !empty($payment->description) ? $payment->description : ''}}</textarea>
                                                   
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>

                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
        <!-- [ Footer ] start -->
        <footer class="footer">
            <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>Copyright ©</span>
                <script>
                    document.write(new Date().getFullYear());
                </script>
            </p>
            <div class="d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Help</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Terms</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacy</a>
            </div>
        </footer>
        <!-- [ Footer ] end -->
    </main>
@endsection
@push('script')
<script>
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });

        endDate.addEventListener('change', function() {
            startDate.max = this.value;
        });
    </script>

@endpush