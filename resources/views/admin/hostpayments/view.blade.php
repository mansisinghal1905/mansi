
@extends('admin.layouts.backend.app')

@push('style')
<style>
    .cust-payment {
        position: relative;
    }

    .cust-payment .cust-paymet-calen {
        position: absolute;
        top: 5px;
        padding: 15px;
    }

    .cust-payment .cust-paymet-calen input {
        border-color: #939497 !important;
        padding: 12px;
        font-size: 14px;
        color: #000;
        margin: 0px 4px;
        height: 45px;
    }


    .cust-payment .cust-paymet-calen button {
        color: #fff;
        border: none;
        background: #3454d1;
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 5px;
        min-height: 45px;
        height: 100%;
        transition: 0.5s;
        box-shadow: 0 5px 15px rgba(40, 60, 80, .15);
    }

    .cust-payment .cust-paymet-calen button:hover {
        background: #000;
        transition: 0.5s;
    }

    .cust-payment .cust-paymet-calen input:focus {
        border-color: #3454d1 !important;
    }
</style>
@endpush
@section('content')
<main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Host Payment History</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.hostpayments.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Host Payment History </li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.hostpayments.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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
                        <div class="card stretch stretch-full">
                            <div class="card-body p-0 cust-payment" >
                            <div class="cust-paymet-calen">
                                <input type="date" id="from_date" name="from_date">
                                <input type="date" id="end_date" name="end_date">
                                <button id="filter">Filter</button>
                                <button id="reset">Reset</button>
                            </div>
                                <div class="table-responsive">
                                    <table class="table table-hover data-table1 table stripe hover nowrap" id="payment1List">
                                        <thead>
                                            <tr>
                                                <th class="wd-30">
                                                    S.No.
                                                </th>
                                                <th>Customer</th>
                                                <th>Amount($)</th>
                                                <th>Purpose Of Payment</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                      
                                    </table>
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

<script type="text/javascript">
		$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table = $('#payment1List').DataTable({
        processing: true,
        serverSide: true,
        scrollY: "400px", // Set the height for the container
        scrollCollapse: true, // Allow the container to collapse when the content is smaller
        pagingType: "simple_numbers", // Use simple pagination (Previous/Next)
        ajax: {
            url: "{{ route('admin.hostpaymenthistoryAjax') }}",
            type: "POST",
            data: function(d) {
                d.host_payment_id = "{{ $id }}";
                d.amount = $('input[name=amount]').val();
                d.created_at = $('input[name=created_at]').val();
                d.description = $('input[name=description]').val();
                d.from_date = $('#from_date').val();
                d.end_date = $('#end_date').val();
            },
            dataSrc: "data"
        },
        columns: [
            { data: "id" },
            { data: "host_customer_id" },
            { data: "amount" },
            { data: "description" },
            { data: "created_at" },
        ],
        columnDefs: [
            { targets: [2,3], orderable: false }
        ],
        paging: true,
        pageLength: 10,
        lengthChange: false,
        searching: true
    });
    // Trigger filtering when the filter button is clicked
    $('#filter').click(function() {
        table.draw();
    });
    // Reset filters and reload the table
    $('#reset').click(function() {
        $('#from_date').val('');
        $('#end_date').val('');
        table.draw();
    });
});

	</script>


@endpush