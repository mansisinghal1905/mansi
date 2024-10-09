@extends('admin.layouts.backend.app')
@push('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/style1.css')}}" />
@endpush
@section('content')

<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Invoice</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.invoice.index') }}">Home</a></li>
                    <li class="breadcrumb-item">View</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                <div class="d-flex align-items-center">
                            <a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-primary d-flex align-items-center">
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
       
        <!-- [ Main Content ] end -->
         <!--invoice html start -->
      <div class="invoice-view-wapper">
         <section class="view-invo-header">
            <div class="container">
               <div class="row">
                  <div class="col-md-6 col-sm-12 col-12">
                     <div class="invo-nuber">
                        <h3>Invoice</h3>
                        <span>{{$invoicepayment->invoice_id}}</span>
                     </div>
                  </div>
                  <div class="col-md-6 col-sm-12 col-12">
                     <div class="invo-nuber "style=float:inline-end;">
                     <h3>Invoice Date</h3>
                     <span>{{ date('Y-m-d', strtotime($invoicepayment->created_at))}}</span>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <section class="invoice-view-details">
            <div class="container">
               <div class="row">
                  <div class="col-md-6 col-sm-12 col-12 invo-from details">
                     <h3>Alphabet Developers</h3>
                     <p>
                        PN. 72, Second Floor<br> Ganesh Vihar Colony<br> Sirsi Road<br> Jaipur-302034<br> 
                        <!-- VAT No: ABG1038492 -->
                     </p>
                  </div>
                  <div class="col-md-6 col-sm-12 col-12 invo-to details">
                     <span class="lable">Invoice To</span>
                     <h3>{{$invoicepayment->getCLient->name}}</h3>
                     <p>{{$invoicepayment->getCLient->address}} 
                        <!-- <br> Rochester<br> kent<br> X12 6DT<br> United Kingdom<br>VAT: This is custom data field -->
                     </p>
                  </div>
               </div>
            </div>
         </section>
         <section class="invoice-date-and-payement">
            <div class="container">
               <div class="row">
                  <div class="col-md-6 col-sm-12 col-12">
                     <div class="invoice-view-dates">
                        <table>
                           <tbody>
                              <!-- <tr>
                                 <td class="x-lang" id="fx-invoice-date-lang">Invoice Date </td>
                                 <td class="x-date"> <span>{{ date('Y-m-d', strtotime($invoicepayment->created_at))}}</span></td>
                              </tr> -->
                              <!-- <tr>
                                 <td class="x-lang">Due Date </td>
                                 <td class="f-value"> <span>08-22-2024</span></td>
                              </tr> -->
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <div class="col-md-6 col-sm-12 col-12">
                     <div class="invoice-view-dates payments">
                        <table>
                           <tbody>
                              <tr>
                                 <td class="x-lang" id="fx-invoice-date-lang">Advance Payments</td>
                                 <td class="f-value"> <span>${{$advancepayment->total_amount ?? 0.00}} </span></td>
                              </tr>
                              <tr>
                                 <td class="x-lang">Balance Due	</td>
                                 <td class="f-value"> <span class="due">${{$dueamount}}</span></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <section class="invoice-list">
            <div class="container">
               <div class="row">
                  <div class="col-md-12 col-sm12 col-12">
                     <div class="table-responsive  invoice-table-wrapper  clear-both">
                        <table class="table table-hover invoice-table ">

                           <thead>
                              <tr>
                              @if($invoicepayment->invoice_type == "weekly")
                                 <th class="text-left">S.No.</th>
                                 <th class="text-left">Task Description</th>
                                 <th class="text-left">Days</th>
                                 <th class="text-left">Working Hour</th>
                                 <th class="text-left">Billing Rate(per/hour)</th>
                                 <th class="text-right" id="bill_col_total">Total Amount</th>
                              @else
                                 <th class="text-left">Project</th>
                                 <th class="text-left">Total Hours</th>
                                 <th class="text-left">Billing Rate(per/hour)</th>
                                 <th class="text-right" id="bill_col_total">Total Amount</th>
                              @endif
                              </tr>
                           </thead>
                           <tbody id="billing-items-container">
                           @if($invoicepayment->invoice_type == "weekly")
                              @if(isset($invoicepayment))
                              @foreach($invoicepayment->taskDetails as $key=>$value)
                              <tr>
                                 <td>{{$key+1}}</td>
                                 <td>{{$value->description}}</td>
                                 <td> {{$value->dayOfWeek}}</td>
                                 <td>{{$value->hours}} {{$hwm1}}&nbsp;</td>
                                 <td>${{$invoicepayment->hourly_rate}} - {{$hwm1}}</td>

                                 <!-- <td>${{$invoicepayment->amount}} ({{$invoicepayment->hourly_rate}} {{$hwm1}})</td> -->
                                 <!-- <td>{{$invoicepayment->amount}}</td> -->
                                 <td class=" text-right">${{$value->task_amount}}</td>
                             
                              </tr>
                              @endforeach
                              @endif
                           @else
                           <td>{{$invoicepayment->getProject->title}}</td>
                           <td>{{$invoicepayment->total_hourss}}</td>
                           <td>{{$invoicepayment->hourly_rate}}</td>
                           <th class="text-right" id="bill_col_total">${{$invoicepayment->taskamounts}}</th>
                           @endif
                             
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <section class="invo-attachments">
            <div class="container">
               <div class="row">
                  <div class="col-md-6 col-sm-12 col-12">
                     <div class="attachments">
                        <!-- <h3>Attachments</h3> -->
                     </div>
                  </div>
                  <div class="col-md-6 col-sm-12 col-12">
                     <div class="total">
                        <table class="invoice-total-table">
                           <!-- <tbody>
                              <tr>
                                 <td>Subtotal</td>
                                 <td>
                                    <span>{{$invoicepayment->subtotal}}</span>
                                 </td>
                              </tr>
                           </tbody> -->
                           <!-- <tbody >
                              <tr >
                                 <td>Discount <span class="x-small">(Fixed)</span></td>
                                 <td><span> -$40.00</span> 
                                 </td>
                              </tr>
                           </tbody> -->
                           <tbody>
                              <tr>
                                 <td class="billing-sums-total">Transtion Charge</td>
                                 <td class="billing-sums-total">
                                    <span>${{$invoicepayment->transtion_charge ?? 0.00}}</span>
                                 </td>
                              </tr>
                           </tbody>
                           <tbody id="invoice-table-section-total">
                              <tr>
                                 <td class="billing-sums-total">Total</td>
                                 <td class="billing-sums-total">
                                    <span>${{$invoicepayment->subtotal}}</span>
                                 </td>
                              </tr>
                           </tbody>


                           
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <section class="invo-view-footer-msg">
            <div class="container">
               <div class="row">
                  <div class="col-md-12 col-sm-12 col-12">
                     <h4>Invoice Terms</h4>
                     <p>Thank you for your business.</p>
                  </div>
               </div>
            </div>
         </section>
      </div>
      <!--invoice html End  -->
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
