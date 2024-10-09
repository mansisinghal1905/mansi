<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Category;
use App\Models\User;
use App\Models\Invoice;
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
            $data = [];
            $data['id'] = ++$i;
            $data['invoice_id'] = $value->invoice_id;
            $data['client_id'] = $value->getCLient->name;
            $data['project_id'] = $value->getProject->title;
            $data['invoice_type'] = ucfirst($value->invoice_type);
            $data['amount'] = $value->amount;
            $data['date'] = $value->date;

            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer clientuserStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.invoice.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.invoice.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '<a href="javascript:void(0)" onclick="deleteInvoicePayments(this)" data-url="' . route('admin.invoicepaymentdestory') . '" class="toolTip deleteInvoicePayments" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            
            $action .= ' <a href="" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Send Invoice">
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

        $projectlist = Project::where("status","1")->get(['id',"title"]);

        return view('admin.invoice_payments.create',compact('invoicepayment','clientlist','projectlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'amount' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
    
        ]);
        $attr = [
            'amount' => 'Amount',
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
                $invoicepayment->amount = $request->amount;
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
        $invoicepayment=Invoice::find($id);
        return view('admin.invoice_payments.view',compact('invoicepayment'));
    }

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
                    'amount' => 'required',
                    'project_id' => 'required',
                    'client_id' => 'required',
                ]);
                $attr = [
                    'amount' => 'Amount',
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
                        $invoicepayment->amount = $request->amount;
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

    
}
