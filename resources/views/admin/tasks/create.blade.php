
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.taskassign.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.taskassign.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $taskassign ? route('admin.taskassign.update', $taskassign->id) : route('admin.taskassign.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($taskassign)
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
                                                <span class="d-block mb-2">Task Assign Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>
                                       
                                        <input type="hidden" name="project_id" value="{{ isset($taskassign->project_id) ? $taskassign->project_id : $project_assign_id }}">

                                        <input type="hidden" name="developer_id" value="{{ isset($taskassign->developer_id) ? $taskassign->developer_id : $developerid->developer_id }}">

                                        <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Task Title: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <input type="text" name="task_title" class="form-control @error('task_title') is-invalid @enderror" value="{{ isset($taskassign->task_title) && !empty($taskassign->task_title) ? $taskassign->task_title : ''}}" id="fullnameInput" placeholder="Task Title" {{ $isDeveloper ? 'readonly' : '' }}>
                                                        </div>
                                                        @error('task_title')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Description: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Task Description" {{ $isDeveloper ? 'readonly' : '' }}>{{ isset($taskassign->description) && !empty($taskassign->description) ? $taskassign->description : ''}}</textarea>
                                                        </div>
                                                        @error('description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                        </div>
                                        @if(Auth::user()->role == 2)
                                        <div class="row mb-4 align-items-center" >
                                            <div class="col-lg-4">
                                                <label for="startDate" class="fw-semibold">Start Date: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <!-- <div class="input-group-text"><i class="feather-user"></i></div> -->
                                                    <input type="datetime-local" id="startDate" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ isset($taskassign->start_date) ? $taskassign->start_date : old('start_date') }}" placeholder="Start Date">
                                                </div>
                                                @error('start_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-4 align-items-center" style="display:none;" id="endDateRow">
                                            <div class="col-lg-4">
                                                <label for="endDate" class="fw-semibold">End Date: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="datetime-local" id="endDate" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ isset($taskassign->end_date) ? $taskassign->end_date : old('end_date') }}" placeholder="End Date">
                                                </div>
                                                @error('end_date')
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

                                                    <select class="form-control @error('task_status') is-invalid @enderror" name="task_status" id="task_status" >
                                                        <option value="">Select Task Status</option>
                                                        
                                                        <option value="pending" {{ old('task_status', $taskassign->task_status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="progress" {{ old('task_status', $taskassign->task_status ?? '') == 'progress' ? 'selected' : '' }}>Progress</option>
                                                        <option value="complete"  {{ old('task_status', $taskassign->task_status ?? '') == 'complete' ? 'selected' : '' }}>Complete</option>
           

                                                    </select>
                                                </div>
                                                @error('task_status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        @endif
                                       
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
document.addEventListener("DOMContentLoaded", function() {
    const taskStatus = document.getElementById('task_status');
    const endDateRow = document.getElementById('endDateRow');
    const endDateInput = document.getElementById('endDate');

    // Function to handle the display logic
    function handleTaskStatusChange() {
        if (taskStatus.value === 'complete') {
            endDateRow.style.display = 'flex'; // Show the row
            endDateInput.setAttribute('required', 'required'); // Make it required
        } else {
            endDateRow.style.display = 'none'; // Hide the row
            endDateInput.removeAttribute('required'); // Remove required
        }
    }

    // Listen for changes in task status
    taskStatus.addEventListener('change', handleTaskStatusChange);

    // Call the function on page load to set initial state
    handleTaskStatusChange();
});
</script>
<!-- <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor1');
</script> -->
@endpush