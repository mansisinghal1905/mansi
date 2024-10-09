
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
                        <h5 class="m-b-10">Invoice</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoice.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $invoicepayment ? route('admin.invoice.update', $invoicepayment->id) : route('admin.invoice.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($invoicepayment)
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
                                                <span class="d-block mb-2">Invoice Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Client: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control @error('client_id') is-invalid @enderror" data-select2-selector="tag" name="client_id" id="client_id">
                                                    @if(count($clientlist) > 0)
                                                        <option value="">Select Client</option>
                                                        @foreach($clientlist as $client)
                                                            <option value="{{ $client->id }}" @if(isset($invoicepayment) && $invoicepayment->client_id == $client->id) selected @endif>{{ ucfirst($client->name) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No Client found</option>
                                                    @endif
                                                </select>
                                                @error('client_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Project: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control @error('project_id') is-invalid @enderror" data-select2-selector="tag" name="project_id" id="project_id">
                                                    @if(count($projectlist) > 0)
                                                        <option value="">Select Project</option>
                                                        @foreach($projectlist as $project)
                                                            <option value="{{ $project->id }}" @if(isset($invoicepayment) && $invoicepayment->project_id == $project->id) selected @endif>{{ ucfirst($project->title) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No Project found</option>
                                                    @endif
                                                </select>
                                                @error('project_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Paid Amount($): </label>
                                            </div>
                                            <div class="col-lg-8">
												<div class="input-group">
                                                 <input type="number" class="form-control @error('paid_amount') is-invalid @enderror" name="paid_amount" value="{{ isset($invoicepayment->paid_amount) && !empty($invoicepayment->paid_amount) ? $invoicepayment->paid_amount : ''}}" id="mailInput" placeholder="Paid Amount">
                                                </div>
                                                @error('paid_amount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> -->

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">

                                                <label class="fw-semibold">Invoice Type: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control @error('invoice_type') is-invalid @enderror" data-select2-selector="tag" name="invoice_type" id="">
                                                        <option value="">Select Invoice Type</option>
                                                        <!-- <option value="hourly" {{ old('invoice_type', $invoicepayment->invoice_type ?? '') == 'hourly' ? 'selected' : '' }}>Hourly</option> -->
                                                        <option value="weekly" {{ old('invoice_type', $invoicepayment->invoice_type ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                                        <option value="monthly"  {{ old('invoice_type', $invoicepayment->invoice_type ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
           
                                                </select>
                                                @error('invoice_type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Input box for hourly rate, hidden by default -->
                                        <div class="row mb-4 align-items-center" id="" >
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Hourly Rate($):</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="hourly_rate" id="hourly_rate" value="{{ isset($invoicepayment->hourly_rate) ? $invoicepayment->hourly_rate : old('hourly_rate') }}" placeholder="Enter hourly rate">
                                            </div>
                                        </div>

                                        <!-- <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Amount($): </label>
                                            </div>
                                            <div class="col-lg-8">
												<div class="input-group">
                                                 <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ isset($invoicepayment->amount) && !empty($invoicepayment->amount) ? $invoicepayment->amount : ''}}" id="mailInput" placeholder="Amount">
                                                </div>
                                                @error('amount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> -->

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Transtion Charge($): </label>
                                            </div>
                                            <div class="col-lg-8">
												<div class="input-group">
                                                 <input type="number" class="form-control" name="transtion_charge" value="{{ isset($invoicepayment->transtion_charge) && !empty($invoicepayment->transtion_charge) ? $invoicepayment->transtion_charge : ''}}" id="mailInput" placeholder="Transtion Charge">
                                                </div>
                                                
                                            </div>
                                        </div> 
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold"> Date: </label>
                                            </div>
                                                <div class="col-lg-8"> 
                                                    <div class="input-group">
                                                    <input type="date" id="" class="form-control" name="date" value="{{ isset($invoicepayment->date) ? $invoicepayment->date : old('date') }}" placeholder="Date">
                                            
                                                    </div>
                                                   
                                                </div>   
                                        </div>
                                        


                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Note: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <textarea class="form-control w-100"
                                                style="width:100% !important;"
                                                id="content" name="notes"  id="addressInput_2" cols="30" rows="3" placeholder="Notes">{{ isset($invoicepayment->notes) && !empty($invoicepayment->notes) ? $invoicepayment->notes : ''}} </textarea>

                                                </div>
                                            </div>
                                        </div> 

                                        <!-- <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Terms & Conditions: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <textarea class="form-control w-100"
                                                style="width:100% !important;"
                                                id="content1" name="terms_condition"  id="addressInput_2" cols="30" rows="3" placeholder="Description"> </textarea>

                                                </div>
                                            </div>
                                        </div>  -->

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

        $(document).ready(function(){
    // Check the selected value on page load
    if ($('#invoice_type').val() === 'weekly') {
        $('#hourlyRateContainer').show();
    }

    // Show or hide the hourly rate input based on the selected invoice type
    $('#invoice_type').change(function(){
        if ($(this).val() === 'weekly') {
            $('#hourlyRateContainer').show();
        } else {
            $('#hourlyRateContainer').hide();
        }
    });
});
</script>

@endpush