<!DOCTYPE html>
<html lang="en">
   <head>
      <!--Meta data Start  -->
      <meta charset="utf-8">
      <meta name="description" content="Marblehead Real Estate Agents">
      <meta name="keywords" content="HTML, CSS, JavaScript">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!--Meta data End -->
      <!--Website page title  -->
      <title>Invoice</title>
    
   </head>
   
   <body>



    <!--invoice html start -->
    <div class="invoice-view-wrapper" style="margin: 15px; max-width: 1080px; margin-left: auto; margin-right: auto; background-color: #fbfcfd; padding: 30px; box-shadow: 0 8px 10px 1px rgba(0, 0, 0, 0.14), 0 3px 14px 2px rgba(0, 0, 0, 0.12), 0 5px 5px -3px rgba(0, 0, 0, 0.2); font: 500 16px Google Sans, Roboto, Helvetica, Arial, sans-serif; width: 100%;">
    
        <!-- Invoice Header -->
        <table class="view-invo-header" style="width: 100%; border-bottom: 1px solid #e1e1e1; padding-bottom: 15px;">
           <tbody><tr>
              <td style="width: 50%; vertical-align: top;">
                 <h3 style="font-size: 24px; color: #455a64; text-transform: capitalize;">Invoice</h3>
                 <span style="font-size: 16px; color: #000;">{{$invoiceNumber}}</span>
              </td>
              <!-- <td style="width: 50%; vertical-align: top;text-align: right;">
                 <h3 style="font-size: 24px; color: #455a64; text-transform: capitalize;">Invoice Date</h3>
                 <span style="font-size: 16px; color: #000;">{{ date('Y-m-d', strtotime($invoicepayment->created_at))}}</span>
              </td> -->
              <!-- <td style="width: 50%; text-align: right;">
                 <span style="font-size: 26px; color: #a4a2a2;">DRAFT</span>
              </td> -->
           </tr>
        </tbody></table>
     
        <!-- Invoice Details -->
        <table class="invoice-view-details" style="width: 100%; padding-top: 15px; padding-bottom: 15px;">
           <tbody><tr>
              <td style="width: 50%; vertical-align: top;">
                 <h3 style="font-size: 20px; font-weight: 600; color: #3454d1;">Alphabet Developers LLP</h3>
                 <p style="font-size: 15px; color: #868c98;">
                    Phone: +918824999499<br>
                    Website: www.alphabetsoftwares.in<br>
                    
                 </p>
              </td>
              <td style="width: 50%; text-align: right;">
                 <span style="font-size: 18px; display: block;">Invoice To</span>
                 <h3 style="font-size: 20px; font-weight: 600; color: #000;">{{$invoicepayment->getCLient->name}}</h3>
                 <p style="font-size: 15px; color: #868c98;">
                    {{$invoicepayment->getCLient->address}}<br>
                    <!-- Rochester<br>
                    Kent<br>
                    X12 6DT<br>
                    United Kingdom<br>
                    VAT: This is custom data field -->
                 </p>
              </td>
           </tr>
        </tbody></table>
     
        <!-- Invoice Dates and Payment -->
        <table class="invoice-date-and-payment" style="width: 100%;background: #f6f6f6;padding: 20px 0px;margin-bottom: 12px;">
           <tbody><tr>
               <td style="width: 50%;vertical-align: top;text-align: left;">
                 <table style="width: 100%;">
                    <tbody><tr>
                       <td style="font-weight: 600;font-size: 15px;color: #5b676d;padding: 10px;width: 30%;">Invoice Date</td>
                       <td style="width: 70%;"><span style="font-size: 15px;">{{ $currentDate }}</span></td>
                    </tr>
                    <tr>
                       <td style="font-weight: 600;font-size: 15px;color: #5b676d;padding: 10px;width: 30%;">Due Date</td>
                       <td style="width: 70%;"><span style="font-size: 15px;">{{ $currentDate }}</span></td>
                    </tr>
                 </tbody></table>
              </td> 
              <td style="width: 50%; text-align: right;">
                 <table style="width: 100%;">
                    <tbody><tr>
                       <td style="font-weight: 600;font-size: 15px;color: #5b676d;padding: 10px;width: 60%;">Fixed Amount</td>
                       <td style="padding: 10px;width: 40%;"><span style="font-size: 15px;">${{$fixedamount->total_amount}}</span></td>
                    </tr>
                    
                 </tbody></table>
              </td>
           </tr>
        </tbody></table>
     
        <!-- Invoice Items List -->
        <table class="invoice-list" style="width: 100%;border-collapse: collapse;margin: 30px 0px;">
           <thead>
              <tr>
                  <th style="text-align: left; font-size: 15px; font-weight: 600; padding: 0.5rem;">Project Name</th>
                  <!-- <th style="text-align: left; font-size: 15px; font-weight: 600; padding: 0.5rem;">Task Description</th>
                  <th style="text-align: left; font-size: 15px; font-weight: 600; padding: 0.5rem;">Days</th>
                  <th style="text-align: left; font-size: 15px; font-weight: 600; padding: 0.5rem;">Working Hour</th>
                  <th style="text-align: left; font-size: 15px; font-weight: 600; padding: 0.5rem;">Billing Rate(per/hour)</th> -->
                  <th style="text-align: left; font-size: 15px; font-weight: 600; padding: 0.5rem;">Total Amount</th>

                 </tr>
           </thead>
           <tbody>
          
              <tr style="border-top: solid 1px #dedede;">
                 <td style="font-size: 15px; color: #5b676d; padding: 0.5rem;">{{$invoicepayment->title}}</td>
                 <td style="font-size: 15px; color: #5b676d; padding: 0.5rem;">{{$fixedamount->total_amount}}</td>
              </tr>
            
              <!-- <tr style="border-top: solid 1px #dedede;">
                 <td style="font-size: 15px; color: #5b676d; padding: 0.5rem;">PSD files</td>
                 <td style="font-size: 15px; color: #5b676d; padding: 0.5rem;">3hrs</td>
                 <td style="font-size: 15px; color: #5b676d; padding: 0.5rem;">Time</td>
                 <td style="font-size: 15px; color: #5b676d; padding: 0.5rem;">15.00</td>
                 <td style="text-align: right; font-size: 15px; color: #5b676d; padding: 0.5rem;">45.00</td>
              </tr> -->
           </tbody>
        </table>
     
       <!-- Attachments Section -->
    <section class="invo-attachments" style="background: #f6f6f6; padding: 0px; margin-bottom: 12px;">
        <table class="container" style="width: 100%; margin: 0 auto; padding: 0 15px; border-collapse: collapse;">
           <tbody><tr>
              <td style="width: 50%; vertical-align: top; padding: 15px;">
                 <div class="attachments">
                    <h3 style="font-size: 17px; color: #455a64;">Attachments</h3>
                 </div>
              </td>
              <td style="width: 50%; vertical-align: top; padding: 15px; text-align: right;">
                 <div class="total">
                    <table class="invoice-total-table" style="width: 100%; border-collapse: collapse;">
                       <tbody>
                        <!-- <tr>
                          <td style="font-size: 15px; font-weight: 600; text-align: right; color: #5b676d; padding: 8px;">Subtotal</td>
                          <td style="font-size: 15px; color: #5b676d; padding: 8px;">$325.00</td>
                       </tr>
                       <tr>
                          <td style="font-size: 15px; font-weight: 600; text-align: right; color: #5b676d; padding: 8px;">Discount <span style="font-size: 12px;">(Fixed)</span></td>
                          <td style="font-size: 15px; color: #5b676d; padding: 8px;">- $40.00</td>
                       </tr>
                       <tr>
                          <td style="font-size: 15px; font-weight: 600; text-align: right; color: #5b676d; padding: 8px;">VAT <span style="font-size: 12px;">(10.00%)</span></td>
                          <td style="font-size: 15px; color: #5b676d; padding: 8px;">$28.50</td>
                       </tr> -->
                       <tr>
                          <td style="font-size: 20px; font-weight: bold; color: #3454d1; padding: 8px;">Total</td>
                          <td style="font-size: 20px; color: #3454d1; padding: 8px;">${{$fixedamount->total_amount}}</td>
                       </tr>
                    </tbody></table>
                 </div>
              </td>
           </tr>
        </tbody></table>
     </section>
     
     <!-- Invoice Footer Message Section -->
     <section class="invo-view-footer-msg" style="padding: 20px 0px; margin-bottom: 12px;">
        <table class="container" style="width: 100%; margin: 0 auto; padding: 0 15px; border-collapse: collapse;">
           <tbody><tr>
              <td style="width: 100%; padding: 15px;">
                 <h4 style="font-size: 18px; color: #455a64; margin: 0;">Invoice Terms</h4>
                 <p style="font-size: 15px; color: #5b676d;">Thank you for your business.</p>
              </td>
           </tr>
        </tbody></table>
     </section>
     
     
    
    
    
    </div>
    
    <!--invoice html End -->
    
    
    
    

</body>

</html>

