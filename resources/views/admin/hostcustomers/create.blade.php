
@extends('admin.layouts.backend.app')

@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Host Customer</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.host-customer.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.host-customer.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $hosting ? route('admin.host-customer.update', $hosting->id) : route('admin.host-customer.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($hosting)
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
                                                <span class="d-block mb-2">Host Customer Information:</span>
                                                <!-- <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span> -->
                                            </h5>
                                        </div>
                                       
                                       

                                            <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Full Name: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ isset($hosting->name) && !empty($hosting->name) ? $hosting->name : ''}}" id="fullnameInput" placeholder="Full Name">
                                                        </div>
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                            </div>

                                            <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Email: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ isset($hosting->email) && !empty($hosting->email) ? $hosting->email : ''}}" id="fullnameInput" placeholder="Enter Email">
                                                        </div>
                                                        @error('email')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                            </div>

							
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold">Amount: </label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="input-group">
                                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ isset($hosting->amount) && !empty($hosting->amount) ? $hosting->amount : ''}}" id="mailInput" placeholder="Amount">
                                                    </div>
                                                    @error('amount')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold">Service Name: </label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="input-group">
                                                    <input type="text" class="form-control" name="service_name" value="{{ isset($hosting->service_name) && !empty($hosting->service_name) ? $hosting->service_name : ''}}" id="phoneInput" placeholder="Service Name">
                                                    </div>
                                                
                                                </div>
                                            </div>
                                            <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold">Subscription: </label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="input-group">
                                                    <select class="form-control" data-select2-selector="category" name="subscription">
                                                        <option value="">Select Subscription</option>
                                                                <option value="monthly" {{ old('subscription', isset($hosting->subscription) ? $hosting->subscription : '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                                <option value="semi-annual" {{ old('subscription', isset($hosting->subscription) ? $hosting->subscription : '') == 'semi-annual' ? 'selected' : '' }}>Semi Annual</option>
                                                                <option value="annual" {{ old('subscription', isset($hosting->subscription) ? $hosting->subscription : '') == 'annual' ? 'selected' : '' }}>Annual</option>
                                                        
                                                    </select>
                                                    </div>
                                                    @error('subscription')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                           

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>

                                        
                                        <!-- End section -->
                                    </form>
                                    </div>
                                </div>

                                <!-- Bank Detail -->
                                
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


@endpush