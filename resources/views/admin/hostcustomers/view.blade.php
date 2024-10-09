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
                    <li class="breadcrumb-item">View</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
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
                <div class="col-xxl-12 col-xl-12">
                    <div class="card border-top-0 p-4">
                        <h6 class="mb-4">Hosting Customer Details</h6>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Full Name:</strong></div>
                            <div class="col-md-8"><p class="text-muted mb-0">{{ ucwords($hosting->name) }}</p></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Amount($):</strong></div>
                            <div class="col-md-8"><p class="text-muted mb-0">{{ $hosting->amount }}</p></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Subscription:</strong></div>
                            <div class="col-md-8"><p class="text-muted mb-0">{{ $hosting->subscription }}</p></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Service:</strong></div>
                            <div class="col-md-8"><p class="text-muted mb-0">{{ ucwords($hosting->service_name) }}</p></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Role:</strong></div>
                            <div class="col-md-8"><p class="text-muted mb-0">{{ $hosting->role == 4 ? 'Technical' : 
                                                ($hosting->role == 5 ? 'Billing' : 
                                                ($hosting->role == 6 ? 'Renewal' : 'Unknown')) }}</p></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Created On:</strong></div>
                            <div class="col-md-8"><p class="text-muted mb-0">{{ date('Y, M d', strtotime($hosting->created_at)) }}</p></div>
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
