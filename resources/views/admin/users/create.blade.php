
@extends('admin.layouts.backend.app')

@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Clients</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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
                            <!-- <div class="card-header p-0">
                               
                                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item flex-fill border-top" role="presentation">
                                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab" data-bs-target="#profileTab" role="tab">Profile</a>
                                    </li>
                                    <li class="nav-item flex-fill border-top" role="presentation">
                                        <a href="javascript:void(0);" class="nav-link" data-bs-toggle="tab" data-bs-target="#passwordTab" role="tab">Bank Information</a>
                                    </li>
                                  
                                </ul>
                            </div> -->
                            <div class="tab-content">
                                
                                <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
                                    <div class="card-body personal-info">

                                    <form action="{{ $user ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($user)
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
                                                <span class="d-block mb-2">Personal Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>
                                       
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Name: </label>
												 <div class="input-group">
                                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ isset($user->name) && !empty($user->name) ? $user->name : ''}}" id="fullnameInput" placeholder="Name">
                                                </div>
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
											 <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Email: </label>
												<div class="input-group">
                                                 <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ isset($user->email) && !empty($user->email) ? $user->email : ''}}" id="mailInput" placeholder="Email">
                                                </div>
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
											<div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Mobile: </label>
												<div class="input-group">
                                                 <input type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ isset($user->phone_number) && !empty($user->phone_number) ? $user->phone_number : ''}}" id="phoneInput" placeholder="Phone">
                                                </div>
                                                @error('phone_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>  
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Designation: </label>
                                                <select class="form-control" data-select2-selector="category" name="category">
                                                    <!-- <option value="">Select Category</option> -->
                                                    @if(count($categorylist) > 0)
                                                        <option value="" >Select Designation</option>
                                                                @if($categorylist)
                                                                    @foreach($categorylist as $id=> $des)
                                                                    <option value="{{$des->id}}" @if(isset($user) &&  in_array($des->id, explode(",",$user->category))) selected @endif>{{ ucfirst($des->name) }}</option>
                                                                    @endforeach
                                                                @endif
                                                    @else
                                                        <option value=''>No Designation found</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Company Name: </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="company_name" value="{{ isset($user->company_name) && !empty($user->company_name) ? $user->company_name : ''}}" id="companyInput" placeholder="Company Name">
                                                    </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Zip Code: </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="zip_code" value="{{ isset($user->zip_code) && !empty($user->zip_code) ? $user->zip_code : ''}}" id="designationInput" placeholder="Zip Code">
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                                <label class="fw-semibold">Country: </label>
                                                <select class="form-control" data-select2-selector="country" name="country_id" id="country_id">
                                                    @if(count($countrylist) > 0)
                                                        <option value="" >Select Country</option>
                                                                @if($countrylist)
                                                                    @foreach($countrylist as $id=> $des)
                                                                    <option value="{{$des->id}}" @if(isset($user) &&  in_array($des->id, explode(",",$user->country_id))) selected @endif>{{ ucfirst($des->name) }}</option>
                                                                    @endforeach
                                                                @endif
                                                    @else
                                                        <option value=''>No Country found</option>
                                                    @endif
                                                </select>
                                            </div>
                                            
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">State: </label>
                                                <select class="form-control" data-select2-selector="state" name="state_id" id="state_id">
                                                    <option value="">Select State</option>
                                                    @if(isset($statelist))
                                                        @foreach($statelist as $row)
                                                            <option value="{{$row->id}}" @if(isset($user) && $row->id == $user->state_id) selected @endif>
                                                                {{$row->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-lg-4">
                                                <label class="fw-semibold">City: </label>
                                            
                                                <select class="form-control" data-select2-selector="city" name="city_id" id="city_id">
                                                    <option value="">Select City</option>
                                                    @if(isset($citylist))
                                                        @foreach($citylist as $row)
                                                            <option value="{{$row->id}}" @if(isset($user) && $row->id == $user->city_id) selected @endif>
                                                                {{$row->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="websiteInput" class="fw-semibold">Website: </label>
                                                <div class="input-group">
                                                <input type="text" class="form-control" name="website" value="{{ isset($user->website) && !empty($user->website) ? $user->website : ''}}" id="websiteInput" placeholder="Website">
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <label for="addressInput_2" class="fw-semibold">Address: </label>
                                                <div class="input-group">
                                                    <textarea class="form-control" name="address"  id="addressInput_2" cols="30" rows="3" placeholder="Address">{{ isset($user->address) && !empty($user->address) ? $user->address : ''}}</textarea>
                                                </div>
                                            </div>

                                            
                                        </div>

                                        
                                        <!-- End section -->

                                        <div class="mb-4 d-flex align-items-center justify-content-between">
                                            <h5 class="fw-bold mb-0 me-4">
                                                <span class="d-block mb-2">Bank Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>
                                       
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Bank Name: </label>
												 <div class="input-group">
                                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ isset($bankInformation->bank_name) && !empty($bankInformation->bank_name) ? $bankInformation->bank_name : ''}}" id="fullnameInput" placeholder="Bank Name">
                                                </div>
                                            </div>
											 <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Account No: </label>
												<div class="input-group">
                                                 <input type="text" class="form-control @error('account_number') is-invalid @enderror" name="account_number" value="{{ isset($bankInformation->account_number) && !empty($bankInformation->account_number) ? $bankInformation->account_number : ''}}" id="mailInput" placeholder="Account Number">
                                                </div>
                                                
                                            </div>
											<div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Account Holder Name: </label>
												<div class="input-group">
                                                 <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" name="account_holder_name" value="{{ isset($bankInformation->account_holder_name) && !empty($bankInformation->account_holder_name) ? $bankInformation->account_holder_name : ''}}" id="phoneInput" placeholder="Account Holder Name">
                                                </div>
                                            </div>  
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">IFSC Code </label>
                                                <div class="input-group">
                                                 <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" name="ifsc_code" value="{{ isset($bankInformation->ifsc_code) && !empty($bankInformation->ifsc_code) ? $bankInformation->ifsc_code : ''}}" id="phoneInput" placeholder="IFSC Code">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Bank Branch Name: </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="branch_name" value="{{ isset($bankInformation->branch_name) && !empty($bankInformation->branch_name) ? $bankInformation->branch_name : ''}}" id="companyInput" placeholder="Bank Branch Name">
                                                    </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold"> Bank Address: </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="bank_address" value="{{ isset($bankInformation->bank_address) && !empty($bankInformation->bank_address) ? $bankInformation->bank_address : ''}}" id="designationInput" placeholder="Bank Address">
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
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
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script> -->
<script type="text/javascript">
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#country_id').select2({
            placeholder: 'Select country', // Your placeholder text here
        });
        $('#state_id').select2({
            placeholder: 'Select state', // Your placeholder text here
        });
        $('#city_id').select2({
            placeholder: 'Select city', // Your placeholder text here
        });
        //Country 
         $("#country_id").change(function() {
            selectedValues = [];
            selectedValues.push($(this).val());
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('admin.getStatelistByCountryId')}}";
            id = "#state_id";
            SelectChangeValue(data,url,id,null);


            selectedValues = [];
            selectedValues.push(0);
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('admin.getCitylistByStateId')}}";
            id = "#city_id";
            SelectChangeValue(data,url,id,null);


           
        });
        //State 
         $("#state_id").change(function() {
            selectedValues = [];
            selectedValues.push($(this).val());
            data = {
                selectedValues:selectedValues
            };
            url = "{{route('admin.getCitylistByStateId')}}";
            id = "#city_id";
            SelectChangeValue(data,url,id,null);

           
        });
        
        function SelectChangeValue(data,url,id,selectedId){
            valuesArray = null;
            if(selectedId!=null)
            {
                valuesArray = selectedId.split(",");
            }
            options="";
              $.ajax({
                type: 'post',
                url: url,
                data: data,
                dataType: "json",
                cache: false,
              //  mimeType: "multipart/form-data",
                //processData: false,
                //contentType: false,
            })
            .done(function(data) {
                if (data.status == true) {
                  
                  var result = data.data;
                  var select_option = ''; 
                  if (id === '#country_id') {
                    select_option = 'Select Country'; 
                  }else if(id === '#state_id'){
                    select_option = 'Select State'; 
                  }else if(id === '#city_id'){
                    select_option = 'Select City'; 
                  }else{
                    select_option = 'Select Region'; 
                  }
                  options="<option selected  value=''>"+select_option+"</option>";
                  
                  $.each(result, function(key,val) {
                    /*if($.inArray(val.id, valuesArray) !== -1)
                    {
                        options+="<option value='"+val.id+"'>"+val.name+"</option>";
                    }
                    else{*/
                     options+="<option  value='"+val.id+"'>"+val.name+"</option>";   
                    /*}*/
                  });
                }
                  $(id).html(options);

            });
        }
    })
</script>

@endpush