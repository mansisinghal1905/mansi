
@extends('admin.layouts.backend.app')

@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Projects</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $project ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($project)
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
                                                <span class="d-block mb-2">Project Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="titleInput" class="fw-semibold">Title: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <!-- <div class="input-group-text"><i class="feather-user"></i></div> -->
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ isset($project->title) ? $project->title : old('title') }}" id="titleInput" placeholder="Title">
                                                </div>
                                                @error('title')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="startDate" class="fw-semibold">Start Date: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <!-- <div class="input-group-text"><i class="feather-user"></i></div> -->
                                                    <input type="date" id="startDate" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ isset($project->start_date) ? $project->start_date : old('start_date') }}" placeholder="Start Date">
                                                </div>
                                                @error('start_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="endDate" class="fw-semibold">End Date: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <!-- <div class="input-group-text"><i class="feather-user"></i></div> -->
                                                    <input type="date" id="endDate" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ isset($project->end_date) ? $project->end_date : old('end_date') }}" placeholder="End Date">
                                                </div>
                                                @error('end_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Client: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <select class="form-control" data-select2-selector="tag" name="client" id="client_id">
                                                    @if(count($clientlist) > 0)
                                                        <option value="">Select Client</option>
                                                        @foreach($clientlist as $client)
                                                            <option value="{{ $client->id }}" @if(isset($project) && $project->client == $client->id) selected @endif>{{ ucfirst($client->name) }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value=''>No Client found</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        

                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fixed-cost-yes" class="fw-semibold">Fixed Cost:</label>
                                            </div>
                                            <div class="col-lg-8"> 
                                                <div class="input-group">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="fixed_cost" id="fixed-cost-yes" value="yes" 
                                                        {{ isset($project->fixed_cost) && $project->fixed_cost === 'yes' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="fixed-cost-yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="fixed_cost" id="fixed-cost-no" value="no" 
                                                        {{ isset($project->fixed_cost) && $project->fixed_cost === 'no' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="fixed-cost-no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>

                                    </div>
                                </div>
                               
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
<script>
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        startDate.addEventListener('change', function() {
            endDate.min = this.value;
        });

        endDate.addEventListener('change', function() {
            startDate.max = this.value;
        });
    </script>

@endpush