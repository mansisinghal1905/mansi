<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Category;
use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentHistory;
use DB;
use Hash;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new Payment;
        $this->Model1 = new PaymentHistory;

        $this->columns = [
            "id",
            "project_id",
            "client_id",
            "total_amount",
            "created_at",
        ];
        $this->columns1 = [
            "id",
            "payment_id",
            "client_id",
            "amount",
            "created_at",
        ];
    }

    

    public function index()
    {
        $payment = Payment::all();
    
        return view('admin.payments.index',compact('payment'));
    }

    public function paymentAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchPayment($request, $this->columns);
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
            $data['client_id'] = $value->getCLient->name;
            $data['project_id'] = $value->getProject->title;
            $data['total_amount'] = $value->total_amount;
            $data['created_at'] = date('Y-m-d', strtotime($value->created_at));

        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            // $action .= '<a href="' . route('admin.payments.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.payments.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Payment History"><i class="fa fa-eye"></i></a>';

            // $action .= '<a href="javascript:void(0)" onclick="deletePayments(this)" data-url="' . route('admin.paymentdestory') . '" class="toolTip deletePayments" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            
            
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
        $payment = null;
        $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);

        $projectlist = Project::where("status","1")->get(['id',"title"]);

        return view('admin.payments.create',compact('payment','clientlist','projectlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'total_amount' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
    
        ]);
        $attr = [
            'total_amount' => 'Amount',
            'project_id' => 'project Name',
            'client_id' => 'Client Name'
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.payments.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
    
                $payment =  Payment::where("client_id",$request->client_id)->first();
                if($payment){
                    $payment->total_amount = $payment->total_amount +  $request->total_amount;
                    $payment->updated_at = date('Y-m-d H:i:s');
                    $payment->save();
                }
                else{
                    $payment = new Payment;
                    $payment->project_id = $request->project_id;
                    $payment->client_id = $request->client_id;
                    $payment->total_amount =  $request->total_amount;
                    $payment->description =  $request->description;
                    $payment->created_at = date('Y-m-d H:i:s');
                    $payment->updated_at = date('Y-m-d H:i:s');
                    $payment->save();
                }
                if($payment){
                    $paymenthistory = new PaymentHistory;
                    $paymenthistory->payment_id = $payment->id;
                    $paymenthistory->project_id = $request->project_id;
                    $paymenthistory->client_id = $request->client_id;
                    $paymenthistory->amount =  $request->total_amount;
                    $paymenthistory->description =  $request->description;
                    $paymenthistory->created_at = date('Y-m-d H:i:s');
                    $paymenthistory->updated_at = date('Y-m-d H:i:s');

                    if ($paymenthistory->save()) {
                        $request->session()->flash('success', 'Payment added successfully');
                        return redirect()->route('admin.payments.index');
                    } else {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.payments.index');
                    }
                }
                else{
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.payments.index');
                }

            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.payments.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $payment=Payment::find($id);
        $payment=PaymentHistory::with('getpayment')->where('payment_id', $id)->get();

        $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);

        $projectlist = Project::where("status","1")->get(['id',"title"]);

        return view('admin.payments.view',compact('payment','clientlist','projectlist','id'));
    }

    public function paymenthistoryAjax(Request $request)
        {
            try
            {

            $request->search = $request->search;
            
            if (isset($request->order[0]['column'])) {
                $request->order_column = $request->order[0]['column'];
                $request->order_dir = $request->order[0]['dir'];
            }
            $records = $this->Model1->fetchPaymentHistroy($request, $this->columns1);
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
                $data['client_id'] = $value->getCLient->name;
                $data['project_id'] = $value->getProject->title;
                $data['amount'] = $value->amount;
                $data['description'] = substr((string)($value->description ?? 'Null'), 0, 20);

                $data['created_at'] = date('Y-m-d', strtotime($value->created_at));

    
                $result[] = $data;

            }
        //  dd($result);
            $data = json_encode([
                'data' => $result,
                'recordsTotal' => count($total),
                'recordsFiltered' => count($total),
            ]);
            return $data;
        }catch(Exception $e){
            dd($e);
        }
        }

}
