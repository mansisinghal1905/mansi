

@extends('admin.layouts.backend.app')
@push('style')
<style>


</style>

@endpush
@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Ticket System</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.ticket-system.index') }}">Ticket</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.ticket-system.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $ticket ? route('admin.ticket-system.update', $ticket->id) : route('admin.ticket-system.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($ticket)
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
                                                <span class="d-block mb-2">Ticket Information:</span>
                                                <!-- <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span> -->
                                            </h5>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            
                                        <div class="col-lg-6">
                                            <label for="fullnameInput" class="fw-semibold">Customer Name: </label>
                                            <div class="input-group">
                                                <!-- Hidden input for customer_id -->
                                                <input type="hidden" name="customer_id" value="{{ Auth::user()->id }}" id="firstnameInput">
                                                <!-- Visible input for display purposes -->
                                                <input type="text" name="{{ Auth::user()->id }}" class="form-control" value="{{ Auth::user()->name }}" id="displayNameInput" placeholder="Customer Name" readonly>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <label for="fullnameInput" class="fw-semibold">Customer Email: </label>
                                            <div class="input-group">
                                                <!-- Hidden input for customer_email -->
                                                <input type="hidden" name="customer_email" value="{{ Auth::user()->email }}" id="mailInput">
                                                <!-- Visible input for display purposes -->
                                                <input type="text" name="customer_email" class="form-control" value="{{ Auth::user()->email }}" id="displayEmailInput" placeholder="Email" readonly>
                                            </div>
                                        </div>
                                        </div>
                                        @if(Auth::user()->role == 1)
                                        <div class="row mb-4 align-items-center">
                                            <!-- <div class="col-lg-4"> -->
                                                <label class="fw-semibold">User: </label>
                                            <!-- </div> -->
                                            <div class="col-lg-8">
                                                <select class="form-control" data-select2-selector="tag" name="user_id" id="user_id">
                                                    @if(count($customerlist) > 0)
                                                        <option value="">Select User</option>
                                                        @foreach($customerlist as $user)
                                                            <option value="{{ $user->id }}" @if(isset($ticket) && $ticket->user_id == $user->id) selected @endif>{{ ucfirst($user->name) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No User found</option>
                                                    @endif
                                                </select>
                                               
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row mb-4 align-items-center">
                                                <!-- <div class="col-lg-4"> -->
                                                    <label for="fullnameInput" class="fw-semibold"> Subject: </label>
                                                <!-- </div> -->
                                                <div class="col-lg-12"> 
                                                    <div class="input-group">
                                                        <textarea class="form-control w-100 @error('subject') is-invalid @enderror"
                                                        style="width:100% !important;"
                                                        name="subject"  id="" placeholder="Subject">{{ isset($ticket->subject) && !empty($ticket->subject) ? $ticket->subject : ''}}</textarea>
                                                    </div>
                                                    @error('subject')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">

                                            
                                            <div class="col-lg-6">
                                                <label for="fullnameInput" class="fw-semibold">Department: </label>
                                                <div class="input-group">
                                                    <select class="form-control @error('department') is-invalid @enderror" data-select2-selector="tag" name="department" id="">

                                                            <option value="">Select Department</option>
                                                            <option value="technical" {{ old('department', isset($ticket->department) ? $ticket->department : '') == 'technical' ? 'selected' : '' }}>Technical</option>
                                                            <option value="billing" {{ old('department', isset($ticket->department) ? $ticket->department : '') == 'billing' ? 'selected' : '' }}>Billing</option>
                                                            <option value="renewal" {{ old('department', isset($ticket->department) ? $ticket->department : '') == 'renewal' ? 'selected' : '' }}>Renewal</option>
                                                    </select>
                                                </div>
                                                @error('department')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="fullnameInput" class="fw-semibold">Priority: </label>
                                                <div class="input-group">
                                                    <select class="form-control " data-select2-selector="tag" name="priority" id="">

                                                            <option value="">Select Priority</option>
                                                            <option value="high" {{ old('priority', isset($ticket->priority) ? $ticket->priority : '') == 'high' ? 'selected' : '' }}>High</option>
                                                            <option value="medium" {{ old('priority', isset($ticket->priority) ? $ticket->priority : '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                                            <option value="low" {{ old('priority', isset($ticket->priority) ? $ticket->priority : '') == 'low' ? 'selected' : '' }}>Low</option>
                                                    </select>
                                                </div>
                                               
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                                <!-- <div class="col-lg-4"> -->
                                                    <label for="fullnameInput" class="fw-semibold"> Message: </label>
                                                <!-- </div> -->
                                                <div class="col-lg-12"> 
                                                    <div class="input-group">
                                                        <textarea class="form-control w-100"
                                                        style="width:100% !important;"
                                                        name="message"  id="" placeholder="Message">{{ isset($ticket->message) && !empty($ticket->message) ? $ticket->message : ''}}</textarea>
                                                    </div>
                                                </div>
                                        </div>

                                        <!-- <div class="row mb-4 align-items-center">
                                            <label for="fullnameInput" class="fw-semibold">Attachments:</label>
                                            <div class="col-lg-12"> 
                                                <div class="input-group">
                                                    <div id="image_preview">
                                                        @if(isset($ticket) && isset($ticket->documents) && count($ticket->documents) > 0)
                                                            @foreach($ticket->documents as $document)
                                                                <img height="50" width="50" src="{{ asset($document->file_path) }}" alt="Document">
                                                            @endforeach
                                                        @else
                                                            <img height="50" width="50" id="previewing" src="{{ asset('public/assets/images/no-image-available.png') }}" alt="">
                                                        @endif
                                                    </div>
                                                    <input type="file" id="file" name="attachment[]" accept=".jpg, .jpeg, .png" class="form-control" multiple>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row mb-4 align-items-center">
                                            <label for="fullnameInput" class="fw-semibold">Attachments:</label>
                                            <div class="col-lg-12"> 
                                                <div class="input-group">
                                                    <div id="image_preview">
                                                        <!-- Display old files if they exist -->
                                                        @if(isset($existingDocuments) && count($existingDocuments) > 0)
                                                            @foreach($existingDocuments as $document)
                                                                @if(in_array(pathinfo($document->file_path, PATHINFO_EXTENSION), ['jpeg', 'jpg', 'png']))
                                                                    <!-- Image preview for old image files -->
                                                                    <img height="50" width="50" src="{{ asset($document->attachment) }}" alt="Document">
                                                                    <!-- Checkbox to remove the document -->
                                                                    <input type="checkbox" name="remove_document[]" value="{{ $document->id }}"> Remove
                                                                @else
                                                                    <!-- Document link for non-image files (docx, pdf) -->
                                                                    <a href="{{ asset($document->attachment) }}" target="_blank">
                                                                        <img height="50" width="50" src="{{ asset('public/assets/images/document-icon.png') }}" alt="Document">
                                                                    </a>
                                                                    <input type="checkbox" name="remove_document[]" value="{{ $document->id }}"> Remove
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <img height="50" width="50" id="previewing" src="{{ asset('public/assets/images/no-image-available.png') }}" alt="">
                                                        @endif
                                                    </div>
                                                    <!-- Input for new file upload -->
                                                    <input type="file" id="file" name="attachment[]" accept=".jpg, .jpeg, .png, .docx, .pdf" class="form-control" multiple>
                                                </div>
                                            </div>
                                        </div>



                                        
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">Submit</button>
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

<script>
    $(document).ready(function () {
    $('.doc-checkbox').change(function () {
        var target = $(this).data('target');
        if ($(this).is(':checked')) {
            $('#' + target).show();
        } else {
            $('#' + target).hide().val('');
        }
    });
});


</script>
@endpush
