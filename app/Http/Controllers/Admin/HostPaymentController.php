<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Category;
use App\Models\HostingCustomer;
use App\Models\HostPayment;
use App\Models\HostPaymentHistory;
use DB;
use Hash;

class HostPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new HostPayment;
        $this->Model1 = new HostPaymentHistory;

        $this->columns = [
            "id",
            "host_customer_id",
            "total_amount",
            "created_at",
        ];
        $this->columns1 = [
            "id",
            "host_payment_id",
            "host_customer_id",
            "amount",
            "created_at",
        ];
    }

    

    public function index()
    {
        $payment = HostPayment::all();
    
        return view('admin.hostpayments.index',compact('payment'));
    }

    public function hostpaymentAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchHostPayment($request, $this->columns);
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
            $data['host_customer_id'] = $value->getHostCustomer->name;
            $data['total_amount'] = $value->total_amount;
            $data['created_at'] = date('Y-m-d', strtotime($value->created_at));

        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.hostpayments.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Payment History"><i class="fa fa-eye"></i></a>';

            $action.="</div>";

            $data['view'] = $action;
            $result[] = $data;

        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        return $data;
    }

    public function exportHostPayments(Request $request)
    {
        // Fetch records (you can customize this query as needed)
        $records = $this->Model->fetchHostPayment($request, $this->columns)->get();
    
        // Define the CSV file name
        $filename = 'host_payments_' . date('Y-m-d') . '.csv';
    
        // Create a file pointer connected to the output stream
        $handle = fopen('php://output', 'w');
    
        // Set the headers for the response
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
    
        // Add the header row to the CSV
        fputcsv($handle, ['ID', 'Host Customer Name', 'Total Amount', 'Created At']);
    
        // Add the data rows
        foreach ($records as $key => $value) {
            fputcsv($handle, [
                $key + 1,
                $value->getHostCustomer->name,
                $value->total_amount,
                date('Y-m-d', strtotime($value->created_at)),
            ]);
        }
        // Close the file pointer
        fclose($handle);
        exit; // Ensure the script stops executing after the download
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $payment = null;
        $customerlist = HostingCustomer::get(['id',"name"]);

        return view('admin.hostpayments.create',compact('payment','customerlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'total_amount' => 'required',
            'host_customer_id' => 'required',
    
        ]);
        $attr = [
            'total_amount' => 'Amount',
            'host_customer_id' => 'Customer Name'
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.hostpayments.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
    
                $payment =  HostPayment::where("host_customer_id",$request->host_customer_id)->first();
                if($payment){
                    $payment->total_amount = $payment->total_amount +  $request->total_amount;
                    $payment->updated_at = date('Y-m-d H:i:s');
                    $payment->save();
                }
                else{
                    $payment = new HostPayment;
                    $payment->host_customer_id = $request->host_customer_id;
                    $payment->total_amount =  $request->total_amount;
                    $payment->description =  $request->description;
                    $payment->created_at = date('Y-m-d H:i:s');
                    $payment->updated_at = date('Y-m-d H:i:s');
                    $payment->save();
                }
                if($payment){
                    $paymenthistory = new HostPaymentHistory;
                    $paymenthistory->host_payment_id = $payment->id;
                    $paymenthistory->host_customer_id = $request->host_customer_id;
                    $paymenthistory->amount =  $request->total_amount;
                    $paymenthistory->description =  $request->description;
                    $paymenthistory->created_at = date('Y-m-d H:i:s');
                    $paymenthistory->updated_at = date('Y-m-d H:i:s');

                    if ($paymenthistory->save()) {
                        $request->session()->flash('success', 'Host Payment added successfully');
                        return redirect()->route('admin.hostpayments.index');
                    } else {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.hostpayments.index');
                    }
                }
                else{
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.hostpayments.index');
                }

            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.hostpayments.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment=HostPaymentHistory::with('gethostpayment')->where('host_payment_id', $id)->get();
        // dd($payment);
        return view('admin.hostpayments.view',compact('payment','id'));
    }

    public function hostpaymenthistoryAjax(Request $request)
        {
            try
            {

            $request->search = $request->search;
            
            if (isset($request->order[0]['column'])) {
                $request->order_column = $request->order[0]['column'];
                $request->order_dir = $request->order[0]['dir'];
            }
            $records = $this->Model1->fetchHostPaymentHistroy($request, $this->columns1);
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
                $data['host_customer_id'] = $value->getHostCustomer->name;
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
