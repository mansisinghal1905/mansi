
@extends('admin.layouts.backend.app')

@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Developers</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.developers.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.developers.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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

                                    <form action="{{ $developer ? route('admin.developers.update', $developer->id) : route('admin.developers.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($developer)
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
                                                <span class="d-block mb-2">Developer Information:</span>
                                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">Following information is publicly displayed, be careful! </span>
                                            </h5>
                                        </div>
                                       
                                       

                                            <div class="row mb-4 align-items-center">
                                                <div class="col-lg-4">
                                                    <label for="fullnameInput" class="fw-semibold"> Full Name: </label>
                                                </div>
                                                    <div class="col-lg-8"> 
                                                        <div class="input-group">
                                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ isset($developer->name) && !empty($developer->name) ? $developer->name : ''}}" id="fullnameInput" placeholder="Full Name">
                                                        </div>
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                
                                            </div>

							
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Email: </label>
                                            </div>
                                            <div class="col-lg-8">
												<div class="input-group">
                                                 <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ isset($developer->email) && !empty($developer->email) ? $developer->email : ''}}" id="mailInput" placeholder="Email">
                                                </div>
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-4 align-items-center">
											<div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Mobile: </label>
                                            </div>
                                            <div class="col-lg-8">
												<div class="input-group">
                                                 <input type="text" class="form-control" name="phone_number" value="{{ isset($developer->phone_number) && !empty($developer->phone_number) ? $developer->phone_number : ''}}" id="phoneInput" placeholder="Phone">
                                                </div>
                                              
                                            </div>
                                        </div>
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Designation: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <select class="form-control" data-select2-selector="category" name="designation">
                                                    <!-- <option value="">Select Category</option> -->
                                                    @if(count($categorylist) > 0)
                                                        <option value="" >Select Designation</option>
                                                                @if($categorylist)
                                                                    @foreach($categorylist as $id=> $des)
                                                                    <option value="{{$des->id}}" @if(isset($user) &&  in_array($des->id, explode(",",$user->designation))) selected @endif>{{ ucfirst($des->name) }}</option>
                                                                    @endforeach
                                                                @endif
                                                    @else
                                                        <option value=''>No Designation found</option>
                                                    @endif
                                                </select>
                                                    <!-- <input type="text" class="form-control" name="designation" value="{{ isset($developer->designation) && !empty($developer->designation) ? $developer->designation : ''}}" id="companyInput" placeholder="Designation"> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-lg-4">
                                                <label for="fullnameInput" class="fw-semibold">Image: </label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                <div id="image_preview">
                                                    @if(isset($developer) && $developer->avatar != null)
                                                        <img height="50" width="50" id="previewing" src="{{ asset($developer->avatar) }}" alt="User Avatar">
                                                    @else
                                                        <img height="50" width="50" id="previewing" src="{{ asset('public/assets/images/no-image-available.png')}}" alt="">
                                                    @endif
                                                    </div>
                                                    <input type="file" id="file" name="avatar" accept=".jpg, .jpeg, .png" class="form-control @error('avatar') is-invalid @enderror">
                                                
                                                    <!-- <input type="text" class="form-control" name="designation" value="{{ isset($developer->designation) && !empty($developer->designation) ? $developer->designation : ''}}" id="companyInput" placeholder="Designation"> -->
                                                </div>
                                                @error('avatar')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
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


@endpush