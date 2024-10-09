
@extends('admin.layouts.backend.app')
@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/addon.css')}}" />
@endpush
@section('content')

@php
    $isDeveloper = auth()->user()->role === 2; 
@endphp

<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Task</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $task ? route('admin.tasks.update', $task->id) : route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">
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
                                                <span class="d-block mb-2">Task Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>
                                       
                                        
                                        <input type="hidden" name="developer_id" value="{{Auth::user()->id}}">

                                        <!-- <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Task Title: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <input type="text" name="task_title" class="form-control @error('task_title') is-invalid @enderror" value="{{ isset($task->task_title) && !empty($task->task_title) ? $task->task_title : ''}}" id="fullnameInput" placeholder="Task Title" {{ $isDeveloper ? 'readonly' : '' }}>
                                                        </div>
                                                        @error('task_title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                        </div> -->
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
                                                    <label for="fullnameInput" class="fw-semibold"> Task : </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Task here....">{{ isset($task->description) && !empty($task->description) ? $task->description : ''}}</textarea>
                                                        </div>
                                                        @error('description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                        </div>
                                         <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Task Hour: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <input type="text" name="hours" id="hoursInput" class="form-control @error('hours') is-invalid @enderror" value="{{ isset($task->hours) && !empty($task->hours) ? $task->hours : ''}}" id="" placeholder="Task Hour">
                                                        </div>
                                                        @error('hours')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                        </div> 
                                       

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="task_status" class="fw-semibold">Task Status: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">

                                                    <select class="form-control @error('task_status') is-invalid @enderror" name="task_status" id="" >
                                                        <option value="">Select Task Status</option>
                                                        
                                                        <option value="pending" {{ old('task_status', $task->task_status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="progress" {{ old('task_status', $task->task_status ?? '') == 'progress' ? 'selected' : '' }}>Progress</option>
                                                        <option value="complete"  {{ old('task_status', $task->task_status ?? '') == 'complete' ? 'selected' : '' }}>Complete</option>
           

                                                    </select>
                                                </div>
                                                @error('task_status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">Save Canges</button>
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

<script>
    document.getElementById('hoursInput').addEventListener('input', function (e) {
        // Allow numbers and colon for the format like "8:30"
        this.value = this.value.replace(/[^0-9:]/g, '');
    });
</script>
<!-- <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor1');
</script> -->
@endpush