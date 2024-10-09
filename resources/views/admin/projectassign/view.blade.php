@extends('admin.layouts.backend.app')

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
                        <h6 class="mb-4">Project Assign Details</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Project:</strong> 
                                <p class="text-muted mb-0">{{ ucfirst($projectassign->getProject->title) }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Developer:</strong> 
                                <p class="text-muted mb-0">{{ ucfirst($projectassign->getDeveloper->name) }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Created On:</strong> 
                                <p class="text-muted mb-0">{{ date('Y, M d', strtotime($projectassign->created_at)) }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Status:</strong> 
                                <p class="text-muted mb-0">{{ $projectassign->status == 1 ? 'Active' : 'Inactive' }}</p>
                            </div>
                        </div>

                        <!-- Tasks Display -->
                        <!-- <h3 class="ass-title">Assign Task</h3>
                        <table border="1" cellpadding="10" cellspacing="0" width="100%" class="associated-custom-table">
                            <thead>
                                <tr>
                                    <th>Task Title</th>
                                    <th>Description</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Task Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($taskassigns->isNotEmpty())
                                    @foreach($taskassigns as $task)
                                        <tr>
                                            <td>{{ $task->task_title }}</td>
                                            <td>{!! Str::limit($task->description, 20) !!}</td>
                                            <td>{{ $task->start_date ? date('Y, M d', strtotime($task->start_date)) :'N/A' }}</td>
                                            <td>{{ $task->end_date ? date('Y, M d', strtotime($task->end_date)) :'N/A' }}</td>
                                            <td>{{ ucfirst($task->task_status) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">No tasks available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table> -->
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
