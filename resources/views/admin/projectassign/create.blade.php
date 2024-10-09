
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
                        <h5 class="m-b-10">Project Assign</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.projects-assign.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.projects-assign.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $projectassign ? route('admin.projects-assign.update', $projectassign->id) : route('admin.projects-assign.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($projectassign)
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
                                                <span class="d-block mb-2">Project Assign Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
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
                                                            <option value="{{ $project->id }}" @if(isset($projectassign) && $projectassign->project_id == $project->id) selected @endif>{{ ucfirst($project->title) }}</option>
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
                                                <label for="Input" class="fw-semibold">Developer: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-select form-control @error('developer_id') is-invalid @enderror" name="developer_id" data-select2-selector="tag">
                                                    @if(count($developerlist) > 0)
                                                        <option value="" >Select User</option>
                                                        @foreach($developerlist as $developers)
                                                            <option value="{{ $developers->id }}" @if(isset($projectassign) && $projectassign->developer_id == $developers->id) selected @endif>{{ ucfirst($developers->name) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No User found</option>
                                                    @endif
                                                </select>
                                                @error('developer_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Project Status: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control @error('project_status') is-invalid @enderror" data-select2-selector="tag" name="project_status" id="project_id">
                                                    @if(count($projectstatus) > 0)
                                                        <option value="" disabled>Select Project</option>
                                                        @foreach($projectstatus as $status)
                                                            <option value="{{ $status->id }}" @if(isset($developers) && $developers->project_status == $status->id) selected @endif>{{ ucfirst($status->name) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No Project Status found</option>
                                                    @endif
                                                </select>
                                                @error('project_status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Priority: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control" data-select2-selector="tag" name="priority" id="priority">
                                                    <!-- <option value="">Select Priority</option>     -->
                                                    <option value="normal" {{ old('priority', $developers->priority ?? 'default') == 'normal' ? 'selected' : '' }}>Normal</option>
                                                    <option value="low" {{ old('priority', $developers->priority ?? 'default') == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="high" {{ old('priority', $developers->priority ?? 'default') == 'high' ? 'selected' : '' }}>High</option>
                                                    <option value="urgent" {{ old('priority', $developers->priority ?? 'default') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                               
                                                </select>
                                               
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
    ClassicEditor.create( document.querySelector( '#editor1' ) )
        .catch( error => {
            console.error( error );
        } );
</script>

<!-- <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor1');
</script> -->
@endpush