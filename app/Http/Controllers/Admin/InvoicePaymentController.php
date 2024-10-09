<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Payment;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
// use Barryvdh\DomPDF\Facade as PDF;
use App\Mail\InvoiceMail;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\File;
use Mail;
use DB;
use Hash;


class InvoicePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new Invoice;

        $this->columns = [
            "id",
            "invoice_id",
            "project_id",
            "client_id",
            "amount",
            "created_at",
        ];
    }

    public static function generateInvoiceNumber($invoice_id) {
        // Prefix for the invoice number
        $prefix = 'INV-';
        
        // Generate a random 6-digit number
        $randomNumber = mt_rand(100000, 999999);
        
        // Concatenate the prefix, random number, and invoice_id
        $invoiceNumber = $prefix . $randomNumber . $invoice_id;
        
        return $invoiceNumber;
    }
    

    public function index()
    {
        $invoicepayment = Invoice::all();
    
        return view('admin.invoice_payments.index',compact('invoicepayment'));
    }

    public function invoicepaymentAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchInvoicePayment($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i = $request->start;
        foreach ($categories as $value) {
            // dd($value);
            $getamount = Payment::select('total_amount')->where(['client_id'=>$value->client_id , 'project_id'=>$value->project_id])->first();            $data = [];
            // dd($getamount->tosql());
            $data['id'] = ++$i;
            $data['invoice_id'] = $value->invoice_id;
            $data['client_id'] = $value->getCLient->name;
            $data['project_id'] = $value->getProject->title;
            $data['invoice_type'] = ucfirst($value->invoice_type);
            $data['amount'] = $value->amount;
            $data['hourly_rate'] =  isset($value->hourly_rate) ? "$".$value->hourly_rate : '0.00';

            // $data['hourly_rate'] =  isset($value->hourly_rate) ? $value->hourly_rate."hrs" : '0:00 hrs';

            $data['paid_amount'] = $getamount->total_amount ?? '0.00';
            $data['date'] = $value->date;

            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer clientuserStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.invoice.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.invoice.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '<a href="javascript:void(0)" onclick="deleteInvoicePayments(this)" data-url="' . route('admin.invoicepaymentdestory') . '" class="toolTip deleteInvoicePayments" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            
            $action .= ' <a href="' . route('admin.send.invoice', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Send Invoice">
               <i class="fa fa-paper-plane"></i>
           </a>';
            
            $action.="</div>";

            $data['view'] = $action;
            // $data['status'] = $status;
            $result[] = $data;

        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $invoicepayment = null;
        $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);

        $projectlist = Project::where("status","1")->where('fixed_cost', '!=','yes')->get(['id',"title"]);

        return view('admin.invoice_payments.create',compact('invoicepayment','clientlist','projectlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            
            'project_id' => 'required',
            'client_id' => 'required',
    
        ]);
        $attr = [
            
            'project_id' => 'project Name',
            'client_id' => 'Client Name'
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.invoice.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                // Generate the invoice number using the invoice ID
                $invoiceNumber = $this->generateInvoiceNumber(1);

                $invoicepayment = new Invoice;
                $invoicepayment->invoice_id = $invoiceNumber;
                $invoicepayment->project_id = $request->project_id;
                $invoicepayment->client_id = $request->client_id;
                // $invoicepayment->amount = $request->amount;
                $invoicepayment->transtion_charge = $request->transtion_charge;
                $invoicepayment->notes = $request->notes;
                $invoicepayment->invoice_type = $request->invoice_type;
                $invoicepayment->hourly_rate = $request->hourly_rate;
                $invoicepayment->date = $request->date;
                if ($invoicepayment->save()) {

                    $request->session()->flash('success', 'Payment added successfully');
                    return redirect()->route('admin.invoice.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.invoice.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.invoice.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */

     
    public function show(string $id)
    {
        $invoicepayment = Invoice::with('getProject')->find($id);
        $advancepayment = Payment::select('total_amount')->where('project_id', $invoicepayment->project_id)->first();

        $hwm = " ";
        $hwm1 = " ";
        $subTotal = 0.00;
        $dueamount = 0.00;

        if ($invoicepayment) {
           
            if ($invoicepayment->invoice_type == "weekly") {

                // $startOfWeek = Carbon::now()->startOfWeek(); // Start of the week (e.g., Monday)
                // $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (e.g., Sunday)

                // Weekly tasks (last week)
                $startOfWeek = Carbon::now()->subWeek()->startOfWeek(); // Start of last week
                $endOfWeek = Carbon::now()->subWeek()->endOfWeek();     // End of last week

                $gettask = Task::where(['project_id' => $invoicepayment->project_id, 'task_status' => 'complete'])
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();
                    
            } 
            elseif ($invoicepayment->invoice_type == "monthly") {
                // Monthly tasks (current month)
                $startOfMonth = Carbon::now()->startOfMonth();  // Start of the current month
                $endOfMonth = Carbon::now()->endOfMonth();      // End of the current month

                $gettask = Task::where(['project_id' => $invoicepayment->project_id, 'task_status' => 'complete'])
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
                  
            }

            if (isset($gettask)) {
                if ($invoicepayment->invoice_type == "weekly") {
                    foreach ($gettask as $k => $value) {
                        $hourly_rate = intval($invoicepayment->hourly_rate);
                        $hourly_amount = floatval($invoicepayment->amount);
                        $working_hour = intval($value->hours);
                        $dayOfWeek = $value->created_at->format('l');
                        $value->dayOfWeek = $dayOfWeek;
 
                        if ($invoicepayment->invoice_type == "weekly") {
                            $hwm = 'Weekly';
                            $hwm1 = 'hour';
                            $taskamount = ($working_hour * $hourly_rate);
                            $value->task_amount = round($taskamount ?? 0.00, 2);
                        } 
                    
                        $subTotal += $value->task_amount;
                    }
                    $invoicepayment->subtotal = $subTotal + $invoicepayment->transtion_charge ?? 0.00;

                    // Handling advance payment
                    $tamount = isset($advancepayment->total_amount) ? intval($advancepayment->total_amount) : 0;

                    // Check if subtotal is smaller than total_amount
                    if ($invoicepayment->subtotal < $tamount) {
                        $dueamount = '0.00';
                    } else {
                        $dueamount = $tamount - $invoicepayment->subtotal;
                    }
                }else{ 

                $total_hours = $gettask->sum('hours');  // Summing the hours for all tasks in the month
    
                $invoicepayment->total_hourss = $total_hours;
                $hourly_rate = intval($invoicepayment->hourly_rate);
                
                $taskamount = ($total_hours * $hourly_rate);
                $invoicepayment->taskamounts = $taskamount;
                // dd($invoicepayment);
                $invoicepayment->subtotal = $taskamount + $invoicepayment->transtion_charge ?? 0.00;
               
                // Handling advance payment
                $tamount = isset($advancepayment->total_amount) ? intval($advancepayment->total_amount) : 0;

                // Check if subtotal is smaller than total_amount
                if ($invoicepayment->subtotal < $tamount) {
                    $dueamount = '0.00';
                } else {
                    $dueamount = $tamount - $invoicepayment->subtotal;
                }
                
                }

            }

            $invoicepayment->taskDetails = $gettask ?? null;
        }
            return view('admin.invoice_payments.view', compact('invoicepayment', 'advancepayment', 'dueamount', 'hwm', 'hwm1'));
        
    }

    // public function show(string $id)
    // {
    //     $invoicepayment=Invoice::find($id);
    //     $advancepayment = Payment::select('total_amount')->where('project_id',$invoicepayment->project_id)->first();
    //     // dd($advancepayment);
    //     $hwm = " ";
    //     $hwm1 = " ";
    //     $subTotal = 0.00;
    //     $dueamount = 0.00;
    //     if($invoicepayment){
    //         if($invoicepayment->invoice_type == "hourly"){
    //             $gettask = Task::where(['project_id'=>$invoicepayment->project_id,'task_status'=>'complete'])->get();
    //         }else{
    //             // $startOfWeek = Carbon::now()->startOfWeek(); // Start of the week (e.g., Monday)

    //             // $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (e.g., Sunday)

    //             $startOfWeek = Carbon::now()->subWeek()->startOfWeek();  // Start of last week
    //             $endOfWeek = Carbon::now()->subWeek()->endOfWeek();      // End of last week


    //             $gettask = Task::where(['project_id'=>$invoicepayment->project_id,'task_status'=>'complete'])
    //                     ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();
    //             // dd($gettask);
    //         }
    //         if(isset($gettask)){
    //         foreach($gettask as $k =>$value){
    //             $hourly_rate = intval($invoicepayment->hourly_rate);
    //             $hourly_amount = floatval($invoicepayment->amount);
    //             $working_hour = intval($value->hours);
    //             // dd($invoicepayment->invoice_type);
    //             $dayOfWeek = $value->created_at->format('l');
    //             $value->dayOfWeek = $dayOfWeek;
    //             if($invoicepayment->invoice_type == "hourly"){
    //                 $hwm = 'Hours';
    //                 $hwm1 = 'hrs';
    //                 $taskamount = ($working_hour/$hourly_rate)*$hourly_amount;
    //                 $value->task_amount= round($taskamount ?? 0.00, 2);
    //             }else{
    //                 $hwm = 'Weekly';
    //                 $hwm1 = 'Weekly Hours';
    //                 $value->task_amount= 0.00;

    //                 $rate = $invoicepayment->amount/7;
    //                 // dd($rate);
    //                 $value->task_amount= round($rate ?? 0.00, 2);
    //             }
    //             $subTotal+=$value->task_amount;
          
    //         }
    //         $invoicepayment->subtotal = $subTotal;
    //         // dd($advancepayment);
    //         if(isset($advancepayment->total_amount)){
    //         $tamount = intval($advancepayment->total_amount) ?? 0;
    //         }else{
    //             $tamount = 0.00;
    //         }
    //         $dueamounts = ($tamount - $invoicepayment->subtotal);
    //         // dd($dueamounts);
    //         // dd($tamount);
    //         // if($tamount < $dueamounts){
    //             // dd($advancepayment->total_amount);
    //             $dueamount = $dueamounts;
    //         // }
    //         // else{
    //         //     $dueamount ="0.00";
    //         // }
    //         // dd($dueamount);
    //         }
    //         $invoicepayment->taskDetails = $gettask ?? null;
    //     }
    //     //  dd($invoicepayment);
    //     return view('admin.invoice_payments.view',compact('invoicepayment','advancepayment','dueamount','hwm','hwm1'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            // $invoicepayment = Invoice::where('id', $id)->first();
            $invoicepayment = Invoice::find($id);
            // dd($invoicepayment);
            
            if (isset($invoicepayment->id)) {
            
                $type = 'edit';
               
                $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);
                
                $projectlist = Project::where("status","1")->get(['id',"title"]);
            
                return view('admin.invoice_payments.create', compact('invoicepayment', 'type','clientlist','projectlist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.invoice.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.invoice.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $invoicepayment = Invoice::where('id', $id)->first();

            if (isset($invoicepayment->id)) {
                $validate = Validator($request->all(),  [
                    'project_id' => 'required',
                    'client_id' => 'required',
                ]);
                $attr = [
                    'project_id' => 'project Name',
                    'client_id' => 'Client Name'
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('admin.invoice.edit', ['id' => $invoicepayment->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $invoicepayment->project_id = $request->project_id;
                        $invoicepayment->client_id = $request->client_id;
                        // $invoicepayment->amount = $request->amount;
                        $invoicepayment->transtion_charge = $request->transtion_charge;

                        $invoicepayment->notes = $request->notes;
                        $invoicepayment->invoice_type = $request->invoice_type;
                        $invoicepayment->hourly_rate = $request->hourly_rate;
                        $invoicepayment->date = $request->date;
                        // dd($invoicepayment);
                        if ($invoicepayment->save()) {

                            $request->session()->flash('success', 'Payment updated successfully');
                            return redirect()->route('admin.invoice.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.invoice.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.invoice.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.invoice.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.invoice.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function invoicepaymentdestory(Request $request)
    {
        $id = $request->id;
        $record = Invoice::findOrFail($id);
        $record->delete();
        return redirect()->route('admin.invoice.index')->with('success', 'Payment deleted successfully.');;
    }

    public function generatePdfAndSendEmail($invoiceId,Request $request)
    {
        // Fetch your invoice data
        $invoicepayment = Invoice::with('getClient')->findOrFail($invoiceId);
        $advancepayment = Payment::select('total_amount')->where('project_id', $invoicepayment->project_id)->first();
        
        $hwm = " ";
        $hwm1 = " ";
        $subTotal = 0.00;
        $dueamount = 0.00;
        
        // if ($invoicepayment) {
        //     if ($invoicepayment->invoice_type == "hourly") {
        //         $gettask = Task::where(['project_id' => $invoicepayment->project_id, 'task_status' => 'complete'])->get();
        //     } else {
        //         $startOfWeek = Carbon::now()->subWeek()->startOfWeek();  // Start of last week
        //         $endOfWeek = Carbon::now()->subWeek()->endOfWeek();      // End of last week
        //         $gettask = Task::where(['project_id' => $invoicepayment->project_id, 'task_status' => 'complete'])
        //             ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();
        //     }
            
        //     if (isset($gettask)) {
        //         foreach ($gettask as $value) {
        //             $hourly_rate = intval($invoicepayment->hourly_rate);
        //             $hourly_amount = floatval($invoicepayment->amount);
        //             $working_hour = intval($value->hours);
        //             $dayOfWeek = $value->created_at->format('l');
        //             $value->dayOfWeek = $dayOfWeek;
                    
        //             if ($invoicepayment->invoice_type == "hourly") {
        //                 $hwm = 'Hours';
        //                 $hwm1 = 'hrs';
        //                 $taskamount = ($working_hour / $hourly_rate) * $hourly_amount;
        //                 $value->task_amount = round($taskamount ?? 0.00, 2);
        //             } else {
        //                 $hwm = 'Weekly';
        //                 $hwm1 = ' hour';
        //                 $taskamount = ($working_hour * $hourly_rate);
        //                 $value->task_amount = round($taskamount ?? 0.00, 2);

        //                 // $value->task_amount = 0.00;
        //                 // $rate = $invoicepayment->amount / 7;
        //                 // $value->task_amount = round($rate ?? 0.00, 2);
        //             }
        //             $subTotal += $value->task_amount;
        //         }

        //         $invoicepayment->subtotal = $subTotal;
        //         // $tamount = $advancepayment->total_amount ?? 0;
        //         // $dueamount = ($tamount - $invoicepayment->subtotal);
        //         // Handling advance payment
        //         $tamount = isset($advancepayment->total_amount) ? intval($advancepayment->total_amount) : 0;

        //         // Check if subtotal is smaller than total_amount
        //         if ($invoicepayment->subtotal < $tamount) {
        //             $dueamount = '0.00';
        //         } else {
        //             $dueamount = $tamount - $invoicepayment->subtotal;
        //         }
        //         $invoicepayment->taskDetails = $gettask ?? null;
        //     }
        // }
        if ($invoicepayment) {
           
            if ($invoicepayment->invoice_type == "weekly") {

                // $startOfWeek = Carbon::now()->startOfWeek(); // Start of the week (e.g., Monday)
                // $endOfWeek = Carbon::now()->endOfWeek();     // End of the week (e.g., Sunday)

                // Weekly tasks (last week)
                $startOfWeek = Carbon::now()->subWeek()->startOfWeek(); // Start of last week
                $endOfWeek = Carbon::now()->subWeek()->endOfWeek();     // End of last week

                $gettask = Task::where(['project_id' => $invoicepayment->project_id, 'task_status' => 'complete'])
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();
                    
            } 
            elseif ($invoicepayment->invoice_type == "monthly") {
                // Monthly tasks (current month)
                $startOfMonth = Carbon::now()->startOfMonth();  // Start of the current month
                $endOfMonth = Carbon::now()->endOfMonth();      // End of the current month

                $gettask = Task::where(['project_id' => $invoicepayment->project_id, 'task_status' => 'complete'])
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
                  
            }

            if (isset($gettask)) {
                if ($invoicepayment->invoice_type == "weekly") {
                    foreach ($gettask as $k => $value) {
                        $hourly_rate = intval($invoicepayment->hourly_rate);
                        $hourly_amount = floatval($invoicepayment->amount);
                        $working_hour = intval($value->hours);
                        $dayOfWeek = $value->created_at->format('l');
                        $value->dayOfWeek = $dayOfWeek;
 
                        if ($invoicepayment->invoice_type == "weekly") {
                            $hwm = 'Weekly';
                            $hwm1 = 'hour';
                            $taskamount = ($working_hour * $hourly_rate);
                            $value->task_amount = round($taskamount ?? 0.00, 2);
                        } 
                    
                        $subTotal += $value->task_amount;
                    }
                    $invoicepayment->subtotal = $subTotal + $invoicepayment->transtion_charge ?? '0.00';

                    // Handling advance payment
                    $tamount = isset($advancepayment->total_amount) ? intval($advancepayment->total_amount) : 0;

                    // Check if subtotal is smaller than total_amount
                    if ($invoicepayment->subtotal < $tamount) {
                        $dueamount = '0.00';
                    } else {
                        $dueamount = $tamount - $invoicepayment->subtotal;
                    }
                }else{ 

                $total_hours = $gettask->sum('hours');  // Summing the hours for all tasks in the month
    
                $invoicepayment->total_hourss = $total_hours;
                $hourly_rate = intval($invoicepayment->hourly_rate);
                
                $taskamount = ($total_hours * $hourly_rate);
                $invoicepayment->taskamounts = $taskamount;
                // dd($invoicepayment);
                $invoicepayment->subtotal = $taskamount + $invoicepayment->transtion_charge ?? 0.00;
               
                // Handling advance payment
                $tamount = isset($advancepayment->total_amount) ? intval($advancepayment->total_amount) : 0;

                // Check if subtotal is smaller than total_amount
                if ($invoicepayment->subtotal < $tamount) {
                    $dueamount = '0.00';
                } else {
                    $dueamount = $tamount - $invoicepayment->subtotal;
                }
                
                }

            }

            $invoicepayment->taskDetails = $gettask ?? null;
        }

        // Render the view as HTML with all the fetched data
        $html = View::make('admin.invoice_payments.invoice_view', [
            'invoicepayment' => $invoicepayment,
            'advancepayment' => $advancepayment,
            'dueamount' => $dueamount,
            'hwm' => $hwm,
            'hwm1' => $hwm1
        ])->render();

        // Convert HTML to PDF using PHP's DOMPDF (you might need to install DOMPDF)
        $pdf = new DOMPDF();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $output = $pdf->output();

        // Save PDF to a temporary file
        $invoiceDirectory = public_path('invoice');
        // Ensure the directory exists, create it if not
        if (!File::exists($invoiceDirectory)) {
            File::makeDirectory($invoiceDirectory, 0755, true);
        }

        // dd($invoiceDirectory);

        // Save the PDF to a temporary file
        $filePath = $invoiceDirectory . '\invoice_' . $invoiceId . '.pdf';
        // dd($filePath);
        file_put_contents($filePath, $output);

        // Send email with PDF attachment
        $this->sendEmailWithAttachment($invoicepayment->getClient->email, $filePath);

        // Optionally, delete the temporary file
        unlink($filePath);

        $request->session()->flash('success', 'Invoice sent successfully');
        return redirect()->route('admin.invoice.index');

    }


    // public function generatePdfAndSendEmail($invoiceId)
    // {
    //     // dd($invoiceId);
    //     // Fetch your invoice data
    //     $invoicepayment = Invoice::with('getCLient')->findOrFail($invoiceId);
    //     // dd($invoicepayment);

    //     // Render the view as HTML
    //     $html = View::make('admin.invoice_payments.view', ['invoice' => $invoicepayment])->render();

    //     // Convert HTML to PDF using PHP's DOMPDF (you might need to install DOMPDF)
    //     $pdf = new DOMPDF();
    //     $pdf->loadHtml($html);
    //     $pdf->setPaper('A4', 'portrait');
    //     $pdf->render();
    //     $output = $pdf->output();

    //     // Save PDF to a temporary file
    //     $invoiceDirectory = public_path('/invoice');
    //     // Ensure the directory exists, create it if not
    //     if (!File::exists($invoiceDirectory)) {
    //         File::makeDirectory($invoiceDirectory, 0755, true);
    //     }

    //     // Save the PDF to a temporary file
    //     $filePath = $invoiceDirectory . '/invoice_' . $invoiceId . '.pdf';
        
    //     file_put_contents($filePath, $output);

    //     // file_put_contents($filePath, $output);

    //     // Send email with PDF attachment
    //     $this->sendEmailWithAttachment($invoicepayment->getCLient->email, $filePath);

    //     // Optionally, delete the temporary file
    //     unlink($filePath);

    //     return response()->json(['message' => 'Invoice sent successfully']);
    // }

    protected function sendEmailWithAttachment($email, $filePath)
    {
        $data = ['message' => 'Please find your invoice attached.'];

        Mail::send([], $data, function ($message) use ($email, $filePath) {
            $message->to($email)
                    ->subject('Your Invoice')
                    ->attach($filePath, [
                        'as' => basename($filePath),
                        'mime' => 'application/pdf',
                    ]);
        });
    }

    // public function pdf($invoiceId)
    // {
    //     $invoice = Invoice::findOrFail($invoiceId); // Retrieve the invoice data

    //     $pdf = PDF::loadView('admin.invoice_payments.view', compact('invoice')); // Load the view and pass data

    //     return $pdf->download('invoice-' . $invoice->id . '.pdf'); // Download the PDF
    // }


    // public function sendInvoice($invoiceId)
    // {
    //     $invoice = Invoice::with('getCLient')->findOrFail($invoiceId);
    //     // $user = $invoice->user; // Assuming the invoice has a user associated

    //     Mail::to($invoice->getCLient->email)->send(new InvoiceMail($invoice));

    //     return response()->json(['message' => 'Invoice sent successfully']);
    // }
    
}
