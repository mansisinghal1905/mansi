
@extends('admin.layouts.backend.app')
@push('style')
<style>
    
.nxl-container {
    display: flex;
    flex-wrap: wrap;
    align-content: space-between;
}

.nxl-container .nxl-content {
    width: 100%;
}

.nxl-container footer.footer {
    width: 100%;
    height: fit-content;
}
.card {
    border-bottom: none !important;
}
</style>
@endpush
@section('content')
 <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ Main Content ] start -->
            <div class="main-content">
            <!-- <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()" class="btn btn-danger btn-xs btn-flat">Allow for Notification</button> -->

                <div class="row">
                    <!-- [Mini] start -->
                     @if(Auth::user()->role == 1)
                    <div class="col-lg-4">
                        <div class="card mb-4 stretch stretch-full">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text">
                                        <i class="feather feather-star"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Tasks Completed</div>
                                        <div class="fs-12 text-muted">{{$total_complete_task}}/{{$total_task}} completed</div>
                                    </div>
                                </div>
                                <div class="fs-4 fw-bold text-dark">{{$total_complete_task}}/{{$total_task}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card mb-4 stretch stretch-full">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text">
                                        <i class="feather feather-airplay"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Project Done</div>
                                        <div class="fs-12 text-muted">{{$total_complete_project}}/{{$total_project}} project</div>
                                    </div>
                                </div>
                                <div class="fs-4 fw-bold text-dark">{{$total_complete_project}}/{{$total_project}}</div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-4 stretch stretch-full">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text">
                                        <i class="feather feather-airplay"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Project Not Started</div>
                                        <div class="fs-12 text-muted">{{$total_notstart_project}}/{{$total_project}} project</div>
                                    </div>
                                </div>
                                <div class="fs-4 fw-bold text-dark">{{$total_notstart_project}}/{{$total_project}}</div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-4 stretch stretch-full">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text">
                                        <i class="feather feather-airplay"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Project Inprogress</div>
                                        <div class="fs-12 text-muted">{{$total_inprogress_project}}/{{$total_project}} project</div>
                                    </div>
                                </div>
                                <div class="fs-4 fw-bold text-dark">{{$total_inprogress_project}}/{{$total_project}}</div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card mb-4 stretch stretch-full">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text">
                                        <i class="feather feather-file-text"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Total Client</div>
                                        <!-- <div class="fs-12 text-muted">0/20 tasks</div> -->
                                    </div>
                                </div>
                                <div class="fs-4 fw-bold text-dark">{{$total_client}}</div>
                            </div>
                        </div>
                    </div>
                    <!-- [Mini] end !-->
                    @endif
                    <!-- [Leads Overview] end -->
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
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->

    
@endsection

