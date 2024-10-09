<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Email Template</title>
   </head>
   <body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 620px;display: block;margin: 20px 0;padding: 30px;">
        <div style="padding: 10px 0px;"><h3 style="font-size: 20px;font-weight: 900;color: #008000;margin-top: 0;text-align: left;">Alphabet Developers</h3></div>
      <p style="margin-top: 0px;font-size: 16px;">Dear <strong>[{{$data['name']}}]</strong>,</p>

      <!-- <a href="{{$data['link']}}">Document Link</a> -->

      <p style="font-size: 15px;">I hope this email finds you well.</p>
      <p style="font-size: 15px;">Please find the attached file as discussed. If you have any questions or need further assistance, feel free to reach out.</p>
      <p style="font-size: 15px;">Thank you!</p>
      <p style="font-size: 15px;font-weight: 600;">Best regards,</p>
      <div style="background: #ffffff;padding: 12px;border: solid 1px #c4c4c4;">
         <h3 style="font-size: 15px;color: #000000;margin: 0;padding-bottom: 5px;">[{{$data['name']}}]</h3>
         <div class="date-email" style="font-size: 15px;padding-bottom: 5px;">[{{ $data['schedule_date']  }}]</div>
         <!-- <div class="attaced-email"> [Your Contact Information]</div> -->
      </div>
      <div style="margin-top: 30px;padding: 12px;text-align: left;"> 
        <div style="text-align: left;width: 100%;"> <span style="color: #000;font-size: 14px;padding: 5px;">
             <a href="mailto:info@alphabetsoftwares.in" target="_blank" style="
    color: #000;
    text-decoration: none;
    font-size: 14px;
    ">email:info@alphabetsoftwares.in </a></span> <span style="font-size: 14px;color: #000;padding: 5px;">
     <a href="tel:0141-6693741" target="_blank" style="color: #000;text-decoration: none;font-size: 14px;">Phone:0141-6693741</a> </span></div> <div style="color: #000;font-size: 14px;padding: 5px;text-align: left;width: 100%;"> Address: PN. 72, Second Floor, Ganesh Vihar Colony, Sirsi Road, Jaipur - 302034</div></div>
    </div>
     
   </body>
</html>
