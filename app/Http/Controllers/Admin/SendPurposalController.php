<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SendPurposal;
use App\Models\QuotationDetail;
use App\Models\QuotationMoreDetail;
use App\Models\User;
use Mail;
use DB;
use Hash;
use Carbon\Carbon;

class SendPurposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new SendPurposal;

        $this->columns = [
            "id",
            "client_id",
            "quatation_id",
            "status",
        ];
    }

    public function index()
    {
        $sendpurposal = SendPurposal::all();
    
        return view('admin.sendpurposal.index',compact('sendpurposal'));
    }

    public function sendpurposalAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchmailQuotation($request, $this->columns);
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
            $data['client_id'] = $value->getClient->name;
            $data['schedule_date'] = date('Y-m-d', strtotime($value->schedule_date));
          
            $data['status'] = '<select class="form-control status-select" name="status" data-id="' . $value->id . '">';
            $data['status'] .= '<option value="pending"' . ($value->status == 'pending' ? ' selected' : '') . '>Pending</option>';
            $data['status'] .= '<option value="approved"' . ($value->status == 'approved' ? ' selected' : '') . '>Approved</option>';
            $data['status'] .= '<option value="rejected"' . ($value->status == 'rejected' ? ' selected' : '') . '>Rejected</option>';
            $data['status'] .= '</select>';

            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            // $action .= '<a href="' . route('admin.sendpurposal.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.sendpurposal.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            //    $action .= '<a href="javascript:void(0)" onclick="deleteQuotation(this)" data-url="' . route('admin.sendpurposaldestory') . '" class="toolTip deleteQuotation" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
 
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
        $sendpurposal = null;
        $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);
        
        return view('admin.sendpurposal.create',compact('sendpurposal','clientlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        // dd($request->all());
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'client_id' => 'required',
        ]);
        $attr = [
            'client_id' => 'Client Name',
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.sendpurposal.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                
                $sendpurposal = new SendPurposal;
                $sendpurposal->client_id = $request->client_id;
                $sendpurposal->schedule_date = $request->schedule_date;
                // $sendpurposal->client_id = $request->client_id;
                $filename = "";
                if ($request->hasfile('document')) {
                    $file = $request->file('document');
                    $filename = time() . $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    // $filename = str_replace('.pdf', $filename);
                    $file->move(public_path('purposaldocument'), $filename);
                }
                if ($filename != "") {
                    $sendpurposal->document = $filename;
                }

                $sendpurposal->created_at = date('Y-m-d H:i:s');
                $sendpurposal->updated_at = date('Y-m-d H:i:s');
                // dd($sendpurposal);
                if ($sendpurposal->save()) {

                    // Retrieve client information using client_id
                    $client = $sendpurposal->getClient;
                    if (Carbon::parse($request->schedule_date)->isSameDay(Carbon::now())) {
                        // Send email to the client with the scheduled date
                        $documentPath = asset('/public/purposaldocument/' . $sendpurposal->document);
                        // Prepare email data
                        $data = [
                            'schedule_date' => $request->schedule_date,
                            'link' => $documentPath,
                            'name' => $client->name
                        ];
                         sendMail($client->email, 'abc', $data);
                          $sendpurposal->mail_status=1;
                          $sendpurposal->save();
                        $request->session()->flash('success', 'Purposal Send successfully');
                        return redirect()->route('admin.sendpurposal.index');   
                    }else{
                        return redirect()->route('admin.sendpurposal.index');   

                    }
                      
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.sendpurposal.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.sendpurposal.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sendpurposal=SendPurposal::find($id);

        return view('admin.sendpurposal.view',compact('sendpurposal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $sendpurposal = SendPurposal::find($id);
            // dd($quotation);
            
            if (isset($sendpurposal->id)) {
            
                $type = 'edit';
                $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);

               
                return view('admin.sendpurposal.create', compact('sendpurposal', 'type','clientlist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.sendpurposal.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.sendpurposal.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     if (isset($id) && $id != null) {

    //         $sendpurposal = SendPurposal::where('id', $id)->first();

    //         if (isset($sendpurposal->id)) {
    //             $validate = Validator($request->all(),  [
    //                 'client_id' => 'required',
            
    //             ]);
    //             $attr = [
    //                 'client_id' => 'Client Name',
    //             ];

    //             $validate->setAttributeNames($attr);

    //             if ($validate->fails()) {
    //                 return redirect()->route('editProjects', ['id' => $sendpurposal->id])->withInput($request->all())->withErrors($validate);
    //             } else {
    //                 try {
                       
    //                     $sendpurposal =  SendPurposal::find($id);
                       
    //                     $sendpurposal->client_id = $request->client_id;
    //                     $sendpurposal->schedule_date = $request->schedule_date;
    //                     // $sendpurposal->client_id = $request->client_id;
    //                     $filename = "";
    //                     if ($request->hasfile('document')) {
    //                         $file = $request->file('document');
    //                         $filename = time() . $file->getClientOriginalName();
    //                         $filename = str_replace(' ', '', $filename);
    //                         // $filename = str_replace('.pdf', $filename);
    //                         $file->move(public_path('purposaldocument'), $filename);
    //                     }
    //                     if ($filename != "") {
    //                         $sendpurposal->document = $filename;
    //                     }

    //                     $sendpurposal->updated_at = date('Y-m-d H:i:s');
    //                     $sendpurposal->updated_at = date('Y-m-d H:i:s');
    //                     if ($sendpurposal->save()) {

    //                         $request->session()->flash('success', 'Purposal update successfully');
    //                         return redirect()->route('admin.sendpurposal.index');
    //                     } else {
                            
    //                         $request->session()->flash('error', 'Something went wrong. Please try again later.');
    //                         return redirect()->route('admin.sendpurposal.index');
    //                     }
    //                 } catch (Exception $e) {
    //                     $request->session()->flash('error', 'Something went wrong. Please try again later.');
    //                     return redirect()->route('admin.sendpurposal.edit', ['id' => $id]);
    //                 }
    //             }
    //         } else {
    //             $request->session()->flash('error', 'Invalid Data');
    //             return redirect()->route('admin.sendpurposal.edit', ['id' => $id]);
    //         }
    //     } else {
    //         $request->session()->flash('error', 'Invalid Data');
    //         return redirect()->route('admin.sendpurposal.edit', ['id' => $id]);
    //     }

    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function quotationdestory(Request $request)
    // {
    //     $id = $request->id;
    //     $record = SendPurposal::findOrFail($id);
    //     $record->status = 2; 
    //     $record->save();
    //     return redirect()->route('admin.sendpurposal.index')->with('success', 'Quotation deleted Successfully.');
    // }

    public function changeSendPurposalStatus(Request $request)
    {
        // dd($request);
        $response = $this->Model->where('id', $request->id)->update(['status' => $request->status]);
        if ($response) {
            return json_encode([
                'status' => true,
                "message" => "Purposal Status Changes Successfully"
            ]);
        } else {
            return json_encode([
                'status' => false,
                "message" => "Status Changes Fails"
            ]);
        }
    }


}
