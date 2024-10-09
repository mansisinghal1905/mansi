
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $task ? route('admin.invoices.update', $task->id) : route('admin.invoices.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($task)
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
                                                            <option value="{{ $client->id }}" @if(isset($task) && $task->client_id == $client->id) selected @endif>{{ ucfirst($client->name) }}</option>
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
                                                            <option value="{{ $project->id }}" @if(isset($task) && $task->project_id == $project->id) selected @endif>{{ ucfirst($project->title) }}</option>
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

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold"> Invoice Date: </label>
                                            </div>
                                                <div class="col-lg-8"> 
                                                    <div class="input-group">
                                                    <input type="date" id="startDate" class="form-control @error('invoice_date') is-invalid @enderror" name="invoice_date" value="{{ isset($task->invoice_date) ? $task->invoice_date : old('invoice_date') }}" placeholder="Invoice Date">
                                            
                                                    </div>
                                                    @error('invoice_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>   
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold"> Due Date: </label>
                                            </div>
                                                <div class="col-lg-8"> 
                                                    <div class="input-group">
                                                    <input type="date" id="startDate" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ isset($task->due_date) ? $task->due_date : old('due_date') }}" placeholder="Due Date">
                                            
                                                    </div>
                                                    @error('due_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>   
                                        </div>
                                       
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="Input" class="fw-semibold">Category: </label>
                                            </div>
                                            <div class="col-lg-8">
                                            <select class="form-control" data-select2-selector="tag" name="category_id">
                                                <!-- <option value="">Select Category</option> -->
                                                @if(count($categorylist) > 0)
                                                    <option value="" >Select Category</option>
                                                            @if($categorylist)
                                                                @foreach($categorylist as $id=> $des)
                                                                <option value="{{$des->id}}" @if(isset($quotation) &&  in_array($des->id, explode(",",$quotation->category_id))) selected @endif>{{ ucfirst($des->name) }}</option>
                                                                @endforeach
                                                            @endif
                                                @else
                                                    <option value=''>No Category found</option>
                                                @endif
                                            </select>
                                               
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Description: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <textarea class="form-control w-100"
                                                style="width:100% !important;"
                                                id="content" name="description"  id="addressInput_2" cols="30" rows="3" placeholder="Description"> </textarea>

                                                </div>
                                            </div>
                                        </div> 

                                        <div class="row mb-4 align-items-center">
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

@endpush