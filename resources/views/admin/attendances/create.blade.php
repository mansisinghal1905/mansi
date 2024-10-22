
@extends('admin.layouts.backend.app')

@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/addon.css')}}" />

@endpush
@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Attendance</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.attendances.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $attendance ? route('admin.attendances.update', $attendance->id) : route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($attendance)
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
                                                <span class="d-block mb-2">Attendance Information:</span>
                                               
                                            </h5>
                                        </div>
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Checkout Time: </label>
                                            </div>
                                                <div class="col-lg-8"> 
                                                    <div class="input-group">
                                                    <input type="time" id="checkoutTime" class="form-control @error('checkout_from_break') is-invalid @enderror" name="checkout_from_break" value="{{ isset($attendance->checkout_from_break) ? $attendance->checkout_from_break : old('checkout_from_break') }}" placeholder="Checkout Time">
                                            
                                                    </div>
                                                    @error('checkout_from_break')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>   
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Checkin Time: </label>
                                            </div>
                                                <div class="col-lg-8"> 
                                                    <div class="input-group">
                                                    <input type="time" id="checkinTime" class="form-control @error('checkin_from_break') is-invalid @enderror" name="checkin_from_break" value="{{ isset($attendance->checkin_from_break) ? $attendance->checkin_from_break : old('checkin_from_break') }}" placeholder="Checkin Time">
                                            
                                                    </div>
                                                    @error('checkin_from_break')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>   
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Message: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <textarea class="form-control w-100"
                                                style="width:100% !important;"
                                                id="" name="message"  id="addressInput_2" placeholder="Message"> </textarea>

                                                </div>
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
<script src="{{ asset('public/assets/js/custom.js')}}"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create( document.querySelector( '#content' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
<script>
    ClassicEditor.create( document.querySelector( '#content1' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
<script>
    document.getElementById('checkinTime').addEventListener('change', function () {
        var checkinTime = document.getElementById('checkoutTime').value;
        var checkoutTime = this.value;

        if (checkinTime && checkoutTime && checkinTime >= checkoutTime) {
            alert('Checkin time must be greater than checkout time.');
            this.value = ''; // Clear the invalid checkout time
        }
    });
</script>
@endpush