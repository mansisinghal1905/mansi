
@extends('admin.layouts.backend.app')
@push('style')

@Endpush
@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Quotation</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.sendpurposal.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Create</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.sendpurposal.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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
                   
                    <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <form action="{{ $sendpurposal ? route('admin.sendpurposal.update', $sendpurposal->id) : route('admin.sendpurposal.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($sendpurposal)
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
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-4">
                                                <h5 class="fw-bold">Purposal Information:</h5>
                                                <span class="fs-12 text-muted">Add items to Purposal</span>
                                            </div>  
                                        </div>
                                    </div>
                                   

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label class="fw-semibold">Client: </label>
                                        </div>
                                        <div class="col-lg-8">
                                           <select class="form-control @error('client_id') is-invalid       @enderror" name="client_id" data-select2-selector="tag">
                                                @if(count($clientlist) > 0)
                                                    <option value="">Select Client</option>
                                                    @foreach($clientlist as $client)
                                                        <option value="{{ $client->id }}" @if(isset($sendpurposal) && $sendpurposal->client_id == $client->id) selected @endif>
                                                            {{ ucfirst($client->name) }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="">No Client found</option>
                                                @endif
                                            </select>
                                            @error('client_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label class="fw-semibold">Schedule Date: </label>
                                        </div>
                                        <div class="col-lg-8">
                                           <input type="date" class="form-control" name="schedule_date" value="{{ isset($sendpurposal->schedule_date) && !empty($sendpurposal->schedule_date) ? $sendpurposal->schedule_date : ''}}" id="companyInput" placeholder="Date Of Birth">
                                        </div>
                                    </div>
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label class="fw-semibold">Upload Document: </label>
                                        </div>
                                        <div class="col-lg-8">
                                           <input type="file" id="file" name="document" accept=".pdf, .txt" class="form-control">
                                        
                                        @if(isset($sendpurposal->document) && !empty($sendpurposal->document))
                                            <p>Current Document: 
                                                <a href="{{ asset($sendpurposal->document) }}" target="_blank">
                                                    {{ $sendpurposal->document }}
                                                </a>
                                            </p>
                                        @endif
                                        </div>
                                    </div>
                                   
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </main>
@endsection
@push('script')

    
    
@endpush