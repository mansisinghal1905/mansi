<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Mail\TicketMail;
use App\Models\HostingCustomer;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use Mail;
use DB;
use Hash;
use Auth;

class HostCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new HostingCustomer;
        // $this->middleware('permission:Ticket-System', ['only' => ['index','store','create','edit','destroy','update']]);

        $this->columns = [
            "id",
            "subscription_no",
            "name",
            "email",
            "amount",
            "service_name",
            "subscripion",
            "end_date",
            "created_at",
        ];
    }

    public static function generateSubscriptionNo($subscription_id) {

		// Generate 4 random digits
        $code = '#' . mt_rand(1000, 9999);

        // Append the ticket_id
        $code .= $subscription_id;
    
        return $code;
	}
    public function index()
    {
        $hosting = HostingCustomer::all();
       
        return view('admin.hostcustomers.index',compact('hosting'));
    }

    public function hostAjax(Request $request)
    {
        // dd($request->start);
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchHost($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i =  $request->start;
        foreach ($categories as $value) {
            
            $data = [];
            $data['id'] = ++$i;
            $data['subscription_no'] = $value->subscription_no;
            $data['name'] = ucwords($value->name);
            $data['email'] = $value->email;
            $data['service_name'] = $value->service_name;
            $data['subscription'] =   ucfirst($value->subscription);
            $data['amount'] = $value->amount;
            // $data['end_date'] = $value->end_date;
            $data['end_date'] = Carbon::parse($value->end_date)->addDays(10)->format('Y-m-d');
            $data['created_at'] = date('Y-m-d', strtotime($value->created_at));


            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer ticketStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";

            // $view = "<a href='" . route('admin.host-customer.show', $value->id) . "' data-status='1' class='badge badge-secondary userStatus'>View</a>";

            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            // $action .= '<a href="' . route('admin.host-customer.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';

            $action .= '<a href="' . route('admin.host-customer.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '<a href="javascript:void(0)" onclick="deleteHosts(this)" data-url="' . route('admin.host-customer.destory') . '" class="toolTip deleteHosts" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';

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
        $hosting = null;
        
        return view('admin.hostcustomers.create',compact('hosting'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request) {
        $input = $request->all();
        $validate = Validator($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'amount' => 'required',
            'subscription' => 'required',
            'service_name' => 'required',
        ]);
    
        $attr = [
            'name' => 'Name',
            'email' => 'Email',
            'amount' => 'Amount',
            'subscription' => 'Subscription',
            'service_name' => 'Service Name',
        ];
        $validate->setAttributeNames($attr);
    
        if ($validate->fails()) {
            return redirect()->route('admin.host-customer.create')
                             ->withInput($request->all())
                             ->withErrors($validate);
        } else {
            try {
                
                $hosting = new HostingCustomer;
                $subscriptionno = $this->generateSubscriptionNo($hosting->id);
                $hosting->subscription_no = $subscriptionno;
                $hosting->name = $request->name;
                $hosting->email = $request->email;
                $hosting->amount = $request->amount;
                $hosting->subscription = $request->subscription;
                $hosting->service_name = $request->service_name;

                if($hosting->subscription == 'monthly'){
                    $tenDaysBeforeEndOfCurrentMonth = Carbon::now()->addMonths();
                    $dateBeforeTenDays = $tenDaysBeforeEndOfCurrentMonth->copy()->subDays(10);
                    $hosting->end_date = $dateBeforeTenDays->toDateString();
                }
                if ($hosting->subscription == 'semi-annual') {
                    
                    $nextDate = Carbon::now()->addMonths(6);
                    // Get the date that is 10 days before the date after 6 months
                    $dateBeforeTenDays = $nextDate->copy()->subDays(10);
                    $hosting->end_date = $dateBeforeTenDays->toDateString();
                }

                if ($hosting->subscription == 'annual') {
               
                    $dateNextYear = Carbon::now()->addYear();
                    // Subtract 10 days to get the end date
                    $dateBeforeTenDays = $dateNextYear->copy()->subDays(10);
                    $hosting->end_date = $dateBeforeTenDays->toDateString();
                }
                $hosting->save();

                $request->session()->flash('success', 'Hosting Customer added successfully');
                return redirect()->route('admin.host-customer.index');
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.host-customer.index');
            }
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hosting = HostingCustomer::find($id);
      
        return view('admin.hostcustomers.view',compact('hosting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            $hosting = HostingCustomer::find($id);
            
            if (isset($hosting->id)) {

                $type = 'edit';
           
                return view('admin.hostcustomers.create', compact('hosting', 'type'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.host-customer.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.host-customer.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        if (isset($id) && $id != null) {

            $hosting = HostingCustomer::find($id);
            if (!$hosting) {
                $request->session()->flash('error', 'Host not found');
                return redirect()->route('admin.host-customer.edit', ['id' => $id]);
            }

            $request->validate([
                'name' => 'required',
                'amount' => 'required',
                'subscription' => 'required',
                'service_name' => 'required',
            ]);

            $attr = [
                'name' => 'Name',
                'amount' => 'Amount',
                'subscription' => 'Subscription',
                'service_name' => 'Service Name',
            ];


            try {

                $hosting->name = $request->name;
                $hosting->email = $request->email;
                $hosting->amount = $request->amount;
                $hosting->subscription = $request->subscription;
                $hosting->service_name = $request->service_name;
                $hosting->updated_at = now();

                if ($hosting->save()) {

                    $request->session()->flash('success', 'Host Customer updated successfully');
                    return redirect()->route('admin.host-customer.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.host-customer.edit', ['id' => $id]);
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.host-customer.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.host-customer.edit', ['id' => $id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function hostdestory(Request $request)
    {
        $id = $request->id;
        $record = HostingCustomer::findOrFail($id);
        $record->delete();

        // BankInformation::where('user_id', $hosting->id)->delete();
        return redirect()->route('admin.host-customer.index')->with('success', 'Host Customer deleted Successfully.');;

    }

    public function ChangeTicketStatus(Request $request)
    {
        $response = $this->Model->where('id', $request->id)->update(['status' => $request->status]);

        if ($response) {
            return json_encode([
                'status' => true,
                "message" => "Status Changes Successfully"
            ]);
        } else {
            return json_encode([
                'status' => false,
                "message" => "Status Changes Fails"
            ]);
        }
    }


   
}
