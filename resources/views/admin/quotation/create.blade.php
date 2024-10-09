
@extends('admin.layouts.backend.app')
@push('style')

@Endpush
@section('content')
<main class="nxl-container">
        <div class="nxl-content">
          
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                   
                    <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <form action="{{ $quotation ? route('admin.quotation.update', $quotation->id) : route('admin.quotation.store') }}" method="POST" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        @if($quotation)
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
                                                <h5 class="fw-bold">Quotation Information:</h5>
                                                <span class="fs-12 text-muted">Add items to Quotation</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered overflow-hidden" id="tab_logic">
                                                    <thead>
                                                        <tr class="single-item">
                                                            <th class="text-center wd-50">#</th>
                                                            <th class="text-center wd-500">Quotation Name</th>
                                                            <th class="text-center wd-600">Short Description</th>
                                                            <!-- <th class="text-center wd-150">Price</th>
                                                            <th class="text-center wd-150">Total</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(@isset($quotation->getQotation))
                                                            @foreach ($quotation->getQotation as $k =>$value )
                                                            <tr id="addr{{ $k }}" class="permissionRow">
                                                                <td>{{ $k+1 }}</td>
                                                                <input type="hidden" name="q_more_id[]" value="{{ $value->id }}"/>
                                                                <td><input type="text" name="quotation_name[]" placeholder="Quotation Name" class="form-control" value="{{ $value->quotation_name }}"></td>
                                                                <td>
                                                                    <input type="text" required name="short_description[]" placeholder="Short Description..." class="form-control @error('short_description') is-invalid @enderror" value="{{ $value->short_description }}">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @else 
                                                            <tr id="addr0" class="permissionRow">
                                                                <td>1</td>
                                                                <input type="hidden" name="q_more_id[]" value=""/>
                                                                <td><input type="text" name="quotation_name[]" placeholder="Quotation Name" class="form-control" value=""></td>
                                                                <td>
                                                                    <input type="text" required name="short_description[]" placeholder="Short Description..." class="form-control @error('short_description') is-invalid @enderror" value="">
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="button" id="delete_row" class="btn btn-md bg-soft-danger text-danger">Delete</button>
                                                <button type="button" id="add_row" class="btn btn-md btn-primary">Add Items</button>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="titleInput" class="fw-semibold">Quotation Subject: </label>
                                        <div class="input-group">
                                            <input type="text" required name="quotation_subject" placeholder="quotation subject" class="form-control @error('quotation_subject') is-invalid @enderror" value="{{ isset($quotation->quotation_subject) && !empty($quotation->quotation_subject) ? $quotation->quotation_subject : ''}}">
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label for="fullnameInput" class="fw-semibold">Category Name: </label>
                                            <select class="form-control" data-select2-selector="country" name="category_id">
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
                                        <div class="col-lg-4">
                                            <label for="startDate" class="fw-semibold">Start Date: </label>
                                                <div class="input-group">
                                                    <input type="date" id="startDate" class="form-control @error('start_date') is-invalid @enderror" name="start_date" value="{{ isset($quotation->start_date) ? $quotation->start_date : old('start_date') }}" placeholder="Start Date">
                                                </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="endDate" class="fw-semibold">End Date: </label>
                                                <div class="input-group">
                                                    <input type="date" id="endDate" class="form-control @error('end_date') is-invalid @enderror" name="end_date" value="{{ isset($quotation->end_date) ? $quotation->end_date : old('end_date') }}" placeholder="End Date">
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="titleInput" class="fw-semibold">Description: </label>
                                        <div class="input-group">
                                            <textarea class="form-control" name="description"  id="editor" cols="30" rows="3" placeholder="Description....">{{ isset($quotation->description) && !empty($quotation->description) ? $quotation->description : ''}}</textarea>
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
<script src="https://cdn.ckeditor.com/4.22.0/standard/ckeditor.js"></script>

<script>
        CKEDITOR.replace('editor');
    </script>
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